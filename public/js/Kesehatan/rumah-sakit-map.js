/**
 * Aceh Data Warehouse - Modul Kesehatan / Rumah Sakit
 * File: public/js/Kesehatan/rumah-sakit-map.js
 *
 * Peta persebaran rumah sakit (Leaflet):
 *  - citra satelit Esri World Imagery + batas 23 kabupaten/kota Aceh
 *  - titik RS (circleMarker) diwarnai sesuai kelas, sama dengan donat rekapulasi
 *  - titik ikut difilter oleh dropdown kelas/pemilik/jenis & klik diagram
 *
 * Data arrive dari Blade lewat <script type="application/json">.
 */

(function () {
    'use strict';

    function init() {
        if (typeof L === 'undefined') {
            console.warn('Leaflet belum termuat. Peta rumah sakit tidak diinisialisasi.');
            return;
        }

        const mapEl    = document.getElementById('map');
        const rsSrc    = document.getElementById('data-rs');
        const kelasSrc = document.getElementById('data-kelas');

        if (!mapEl || !rsSrc) return;

        const daftarRs    = JSON.parse(rsSrc.textContent || '[]');
        const daftarKelas = kelasSrc ? JSON.parse(kelasSrc.textContent || '[]') : [];

        // label kelas -> warna (identik dengan warna donat rekapulasi per kelas)
        const warnaKelas = new Map(daftarKelas.map((k) => [k.label, k.warna]));

        const esc = (v) => String(v ?? '-').replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
        }[c]));

        // "Kabupaten Aceh Barat" -> "aceh barat", "Kota Banda Aceh" -> "banda aceh"
        const normWilayah = (s) => String(s || '')
            .replace(/^(Kabupaten |Kota )\s*/i, '')
            .trim()
            .toLowerCase();

        const map = L.map(mapEl, { scrollWheelZoom: false, zoomControl: true })
            .setView([4.7, 96.8], 8);

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, Maxar, Earthstar Geographics, and the GIS User Community',
            maxZoom: 19,
        }).addTo(map);
        map.attributionControl.setPosition('bottomleft');

        // ------------------------------------------- pane khusus titik RS
        // Leaflet menaruh L.CircleMarker dan L.GeoJSON di pane yang sama (overlayPane).
        // Karena GeoJSON baru ditambahkan setelah fetch selesai, path batas wilayah
        // digambar DI ATAS titik RS dan menyerap klik (pointer-events: auto).
        // Pane ini elevating titik RS di atas overlayPane tanpa mengubah layer wilayah.
        map.createPane('paneTitikRs');
        map.getPane('paneTitikRs').style.zIndex = 450;

        // ----------------------------------------------- layer batas wilayah
        const geojsonUrl = document.querySelector('meta[name="geojson-url"]')?.content;

        // filter yang sedang berlaku = sumber kebenaran jumlah RS per wilayah
        let filterAktif = { pemilik: '', kelas: '', jenis: '' };

        // Layer batas wilayah, disimpan agar tooltip bisa di refresh saat filter berubah
        let geoLayer = null;

        function deskripsiFilter(f) {
            const bagian = [];
            if (f.kelas)    bagian.push('Kelas ' + f.kelas);
            if (f.pemilik)  bagian.push('Pemilik ' + f.pemilik);
            if (f.jenis)    bagian.push('Jenis ' + f.jenis);
            return bagian.join(', ');
        }

        // jumlah RS per wilayah untuk filter yang sedang aktif
        function hitungRsPerWilayah() {
            const hasil = new Map();
            titik.forEach((t) => {
                const ok = (!filterAktif.pemilik || t.rs.pemilik === filterAktif.pemilik)
                    && (!filterAktif.kelas   || t.rs.kelas   === filterAktif.kelas)
                    && (!filterAktif.jenis   || t.rs.jenis   === filterAktif.jenis);
                if (!ok) return;
                const k = normWilayah(t.rs.kota);
                hasil.set(k, (hasil.get(k) || 0) + 1);
            });
            return hasil;
        }

        // teks tooltip wilayah, mis. "Aceh Barat / 2 Rumah Sakit Kelas A"
        function teksTooltip(nama, hitungan) {
            const jml = hitungan.get(normWilayah(nama)) || 0;
            const filter = deskripsiFilter(filterAktif);
            return `<strong>${esc(nama)}</strong> / ${jml} Rumah Sakit${filter ? ' ' + esc(filter) : ''}`;
        }

        // refresh tooltip seluruh wilayah (dipanggil setiap filter berubah)
        function perbaruiTooltipWilayah() {
            if (!geoLayer) return;
            const hitungan = hitungRsPerWilayah();
            geoLayer.eachLayer((layer) => {
                const props = (layer.feature && layer.feature.properties) || {};
                layer.setTooltipContent(teksTooltip(props.nama || '-', hitungan));
            });
        }

        if (geojsonUrl) {
            fetch(geojsonUrl)
                .then((res) => res.json())
                .then((geojson) => {
                    const hitungan = hitungRsPerWilayah();

                    geoLayer = L.geoJSON(geojson, {
                        style: {
                            color: 'rgba(255, 255, 255, .9)',
                            weight: 1.4,
                            fillColor: '#ffffff',
                            fillOpacity: 0.04,
                        },
                        onEachFeature: (feature, layer) => {
                            const nama = feature.properties.nama || '-';
                            layer.bindTooltip(teksTooltip(nama, hitungan), {
                                sticky: true,
                                className: 'map-tip',
                            });
                        },
                    }).addTo(map);

                    // geoJson selesai dimuat -> sinkronkan tooltip dengan filter yang berlaku
                    perbaruiTooltipWilayah();
                })
                .catch((err) => console.error('Gagal memuat peta GeoJSON:', err));
        }

        // ------------------------------------------------------ titik-titik RS
        const titik = [];

        daftarRs.forEach((r) => {
            const lat = Number(r.lat);
            const lng = Number(r.lng);

            // lewati koordinat tidak valid (null / NaN / di luar rentang bumi)
            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
            if (lat === 0 && lng === 0) return;
            if (lat < -90 || lat > 90 || lng < -180 || lng > 180) return;

            const warna = warnaKelas.get(r.kelas) || '#64748b';

            const marker = L.circleMarker([lat, lng], {
                pane: 'paneTitikRs',
                radius: 7,
                color: '#ffffff',
                weight: 1.5,
                fillColor: warna,
                fillOpacity: 0.9,
            });

            marker.bindPopup(
                `<div class="rs-popup">
                    <div class="rs-popup-title">${esc(r.nama)}</div>
                    <table>
                        <tr><th>Kelas</th><td><span class="rs-dot" style="background:${warna}"></span>${esc(r.kelas)}</td></tr>
                        <tr><th>Jenis</th><td>${esc(r.jenis)}</td></tr>
                        <tr><th>Pemilik</th><td>${esc(r.pemilik)}</td></tr>
                        <tr><th>Kab/Kota</th><td>${esc(r.kota)}</td></tr>
                        <tr><th>Alamat</th><td>${esc(r.alamat)}</td></tr>
                        <tr><th>Telp</th><td>${esc(r.telp)}</td></tr>
                    </table>
                </div>`,
                { maxWidth: 320 }
            );

            marker.addTo(map);
            titik.push({ marker, rs: r });
        });

        if (titik.length) {
            map.fitBounds(
                L.featureGroup(titik.map((t) => t.marker)).getBounds(),
                { padding: [16, 16] }
            );
        }

        // ----------------------------------------------------------- legenda
        if (daftarKelas.length) {
            const legenda = L.control({ position: 'bottomright' });

            legenda.onAdd = function () {
                const div = L.DomUtil.create('div', 'rs-map-legend');
                div.innerHTML =
                    '<div class="rs-map-legend-title">Kelas RS</div>' +
                    daftarKelas
                        .map(
                            (k) =>
                                `<div class="rs-map-legend-item"><span class="rs-dot" style="background:${k.warna}"></span>${esc(k.label)}<b>${k.jumlah}</b></div>`
                        )
                        .join('');
                return div;
            };

            legenda.addTo(map);
        }

        // -------------------------------------------- filter dari halaman
        function terapkanFilter(filter) {

            filterAktif = {
                pemilik: filter.pemilik || '',
                kelas: filter.kelas   || '',
                jenis: filter.jenis   || '',
            }

            const cocok = [];

            titik.forEach((t) => {
                const ok =
                    (!filterAktif.pemilik || t.rs.pemilik === filterAktif.pemilik) &&
                    (!filterAktif.kelas   || t.rs.kelas   === filterAktif.kelas) &&
                    (!filterAktif.jenis   || t.rs.jenis   === filterAktif.jenis);

                if (ok) {
                    cocok.push(t);
                    if (!map.hasLayer(t.marker)) t.marker.addTo(map);
                } else if (map.hasLayer(t.marker)) {
                    map.removeLayer(t.marker);
                }
            });

            perbaruiTooltipWilayah();

            if (cocok.length) {
                map.fitBounds(
                    L.featureGroup(cocok.map((t) => t.marker)).getBounds(),
                    { padding: [16, 16], maxZoom: 14 }
                );
            }

            return cocok.length;
        }

        // event 'rs:filter' dikirim oleh script halaman (dropdown + klik diagram)
        document.addEventListener('rs:filter', (e) => terapkanFilter(e.detail || {}));

        // terapkan state filter awal
        const fPemilik = document.getElementById('filterPemilik');
        const fKelas   = document.getElementById('filterKelas');
        const fJenis   = document.getElementById('filterJenis');

        if (fPemilik && fKelas && fJenis) {
            terapkanFilter({
                pemilik: fPemilik.value,
                kelas: fKelas.value,
                jenis: fJenis.value,
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();