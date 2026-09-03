(function () {
    'use strict';
    
    // Pastikan Leaflet sudah dimuat
    if (typeof L === 'undefined') {
        console.warn('Leaflet belum termuat. Peta tidak dapat diinisialisasi.');
        return; 
    }

    const mapEl = document.getElementById('aceh-map');
    const yearSelect = document.getElementById('filter-tahun');
    const kabSelect = document.getElementById('filter-kabupaten');
    
    if (!mapEl) return;

    const fmt = (n) => new Intl.NumberFormat('id-ID').format(n);
    
    // Inisialisasi Peta
    const map = L.map(mapEl, { scrollWheelZoom: false }).setView([4.7, 96.8], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 20,
    }).addTo(map);

    let geoLayer = null;
    let popByKode = new Map();
    let popByName = new Map();
    let currentTahun = null;

    // Fungsi normalisasi (dari kode asli Anda, sangat bagus!)
    const normKode = (k) => String(k || '').replace(/\D/g, '');
    const normName = (n) => String(n || '')
        .toUpperCase()
        .replace(/^KABUPATEN\s+/, '')
        .replace(/^KAB.?\s+/, '')
        .replace(/^KOTA\s+/, '')
        .replace(/[^A-Z]/g, '');

    function lookup(props) {
        return popByName.get(normName(props.nama)) || popByKode.get(normKode(props.kode)) || null;
    }

    function getColor(pop) {
        return pop > 500000 ? '#7f0000' :
               pop > 400000 ? '#b30000' :
               pop > 300000 ? '#d7301f' :
               pop > 200000 ? '#ef6548' :
               pop > 0       ? '#fcbba1' : '#e3e7ee';
    }

    function styleFeature(feature) {
        const d = lookup(feature.properties);
        return {
            color: '#ffffff',
            weight: 1,
            fillColor: getColor(d ? d.jumlah_penduduk : 0),
            fillOpacity: 0.8,
        };
    }

    function tooltipHtml(props) {
        const d = lookup(props);
        if (!d) {
            return `<strong>${props.nama}</strong><br>Tidak ada data untuk tahun ${currentTahun ?? '-'}`;
        }
        const growth = (d.pertumbuhan_persen === null || d.pertumbuhan_persen === undefined)
            ? ''
            : `<br>Pertumbuhan: ${d.pertumbuhan_persen > 0 ? '+' : ''}${d.pertumbuhan_persen}%`;
            
        return `<strong>${d.nama}</strong><br>
                Tahun ${currentTahun}: ${fmt(d.jumlah_penduduk)} ${(d.satuan || 'jiwa').toLowerCase()}<br>
                Peringkat ${d.peringkat} dari ${popByKode.size}${growth}`;
    }

    function onEachFeature(feature, layer) {
        layer.bindTooltip('', { sticky: true, className: 'map-tip' });
        layer.on({
            mouseover: (e) => {
                e.target.setStyle({ weight: 3, fillOpacity: 0.95 });
                e.target.bringToFront();
                e.target.setTooltipContent(tooltipHtml(feature.properties));
            },
            mouseout: (e) => {
                if (geoLayer) geoLayer.resetStyle(e.target);
            },
            click: () => {
                const d = lookup(feature.properties);
                if (kabSelect && d) {
                    kabSelect.value = d.kode;
                    kabSelect.dispatchEvent(new Event('change'));
                }
            },
        });
    }

    // --- ADAPTASI UNTUK LARAVEL API ---
    async function loadPopulationForYear(tahun) {
        // Gunakan route Laravel, bukan map.php
        const url = `/api/penduduk/map${tahun ? '?tahun=' + tahun : ''}`;
        
        try {
            const res = await fetch(url);
            const body = await res.json();
            
            // Sesuaikan dengan struktur JSON Laravel { success: true, data: { tahun, kabupaten } }
            if (!body.success) throw new Error(body.message || 'Gagal memuat data peta');
            
            currentTahun = body.data.tahun;
            popByKode = new Map();
            popByName = new Map();
            
            body.data.kabupaten.forEach((row) => {
                popByKode.set(normKode(row.kode), row);
                popByName.set(normName(row.nama), row);
            });
            
            if (geoLayer) geoLayer.setStyle(styleFeature);
            
        } catch (err) {
            console.error('Gagal memuat data peta:', err);
        }
    }

    async function initMap() {
        try {
            // Gunakan asset() helper Laravel untuk path GeoJSON
            const geojsonUrl = document.querySelector('meta[name="geojson-url"]')?.content || '/assets/data/aceh-kabupaten.geojson';
            
            const geojson = await fetch(geojsonUrl).then((r) => r.json());
            
            // Load data tahun terbaru saat inisialisasi
            await loadPopulationForYear(null); 
            
            geoLayer = L.geoJSON(geojson, { 
                style: styleFeature, 
                onEachFeature 
            }).addTo(map);
            
            map.fitBounds(geoLayer.getBounds(), { padding: [12, 12] });
            
        } catch (err) {
            console.error('Gagal memuat peta GeoJSON:', err);
        }
    }

    // Jalankan inisialisasi
    initMap();

    // Event Listener: Update peta saat dropdown tahun berubah
    if (yearSelect) {
        yearSelect.addEventListener('change', () => {
            loadPopulationForYear(yearSelect.value);
        });
    }

})();