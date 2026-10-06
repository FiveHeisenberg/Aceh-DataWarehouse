/**
 * Aceh Data Warehouse - Modul Dispenda / Ringkasan Pendapatan
 * File: public/js/Dispenda/dashboard.js
 *
 * Menangani: peta choropleth Leaflet, grafik tren (Chart.js), dan pencarian tabel wilayah.
 * Data arrives dari Blade lewat <script type="application/json">.
 */
(function () {
    'use strict';

    const rupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

    function formatMiliar(juta) {
        return juta >= 1000
            ? 'Rp ' + (juta / 1000).toLocaleString('id-ID', { maximumFractionDigits: 2 }) + ' T'
            : 'Rp ' + juta.toLocaleString('id-ID') + ' M';
    }

    // Setara d3.scaleLinear().domain([min,max]).range(['#d1fae5','#0f766e'])
    const LOW = [0xd1, 0xfa, 0xe5];
    const HIGH = [0x0f, 0x76, 0x6e];

    function makeScale(min, max) {
        const span = max - min;
        return (v) => {
            const t = span > 0 ? Math.min(1, Math.max(0, (v - min) / span)) : 1;
            const c = LOW.map((lo, i) => Math.round(lo + (HIGH[i] - lo) * t));
            return 'rgb(' + c.join(',') + ')';
        };
    }

    // ------------------------------------------------------------- tabel cari
    const search = document.getElementById('wilayah-search');
    const tbody = document.getElementById('wilayah-tbody');
    const counter = document.getElementById('wilayah-count');

    if (search && tbody) {
        const rows = Array.from(tbody.querySelectorAll('tr[data-nama]'));
        const emptyRow = document.getElementById('wilayah-empty');
        const total = rows.length;

        search.addEventListener('input', () => {
            const keyword = search.value.trim().toLowerCase();
            let shown = 0;

            rows.forEach((row) => {
                const match = row.dataset.nama.includes(keyword);
                row.hidden = !match;
                if (match) shown++;
            });

            if (emptyRow) emptyRow.hidden = shown > 0;
            if (counter) counter.textContent = shown + ' baris';
        });

        if (counter) counter.textContent = total + ' baris';
    }

    // ------------------------------------------------------------ grafik tren
    const trenEl = document.getElementById('chart-tren');
    const trenSrc = document.getElementById('data-tren');

    if (trenEl && trenSrc && typeof Chart !== 'undefined') {
        const taxTrend = JSON.parse(trenSrc.textContent || '[]');
        const labels = taxTrend.map((r) => r.month);
        // Cast eksplisit: PDO bisa mengirim string, Chart.js tidak meng-cast sendiri.
        const values = taxTrend.map((r) => Number(r.pajak) || 0);

        new Chart(trenEl, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Penerimaan Pajak',
                        data: values,
                        borderColor: '#0d9488',
                        borderWidth: 3,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#0d9488',
                        pointHoverRadius: 6,
                        fill: true,
                        backgroundColor: (ctx) => {
                            const { ctx: c, chartArea } = ctx.chart;
                            if (!chartArea) return 'rgba(13, 148, 136, .15)';
                            const g = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            g.addColorStop(0, 'rgba(13, 148, 136, .25)');
                            g.addColorStop(1, 'rgba(13, 148, 136, .02)');
                            return g;
                        },
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (item) => rupiah(item.parsed.y),
                        },
                    },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#6b7280' } },
                    y: {
                        grid: { color: '#e5e7eb', borderDash: [3, 3] },
                        ticks: {
                            color: '#6b7280',
                            callback: (v) => Number(v / 1e6).toLocaleString('id-ID') + ' Jt',
                        },
                    },
                },
            },
        });
    }

    // ------------------------------------------------------------------- peta
    const mapEl = document.getElementById('peta-pendapatan');
    const revenueSrc = document.getElementById('data-revenue');

    if (mapEl && typeof L !== 'undefined' && revenueSrc) {
        const revenue = JSON.parse(revenueSrc.textContent || '[]');
        const byName = new Map(revenue.map((r) => [r.name, Number(r.value) || 0]));
        const values = revenue.map((r) => Number(r.value) || 0);
        const colorScale = makeScale(values.length ? Math.min(...values) : 0, values.length ? Math.max(...values) : 1);

        // Peta Asli memakai "Kota Banda Aceh", data DWH memakai "Banda Aceh".
        const stripPrefiks = (s) => String(s || '').replace(/^(Kabupaten |Kota )\s*/i, '');

        const map = L.map(mapEl, { scrollWheelZoom: false, zoomControl: true }).setView([4.9, 96.8], 8);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 20,
        }).addTo(map);

        const geojsonUrl = document.querySelector('meta[name="geojson-url"]')?.content;

        let geoLayer = null;

        fetch(geojsonUrl)
            .then((r) => r.json())
            .then((geojson) => {
                geoLayer = L.geoJSON(geojson, {
                    style: (feature) => ({
                        color: '#ffffff',
                        weight: 0.5,
                        fillColor: colorScale(byName.get(stripPrefiks(feature.properties.name)) ?? 0),
                        fillOpacity: 1,
                    }),
                    onEachFeature: (feature, lyr) => {
                        const nama = feature.properties.name;
                        const nilai = byName.get(stripPrefiks(nama)) ?? 0;

                        lyr.bindTooltip('<strong>' + nama + '</strong><br>' + formatMiliar(nilai), {
                            sticky: true,
                            className: 'map-tip',
                        });

                        lyr.on({
                            mouseover: (e) => e.target.setStyle({ fillColor: '#134e4a', weight: 1.5 }),
                            mouseout: (e) => geoLayer.resetStyle(e.target),
                        });
                    },
                }).addTo(map);

                map.fitBounds(geoLayer.getBounds(), { padding: [12, 12] });
            })
            .catch((err) => console.error('Gagal memuat peta GeoJSON:', err));
    }
})();
