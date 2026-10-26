(function () {
    'use strict';

    function init() {
        if (typeof L === 'undefined') {
            console.warn('Leaflet belum termuat. Peta populasi ternak tidak diinisialisasi.');
            return;
        }

        const mapEl = document.getElementById('map');
        const dataEl = document.getElementById('data-populasi-ternak');
        const filterEl = document.getElementById('filterJenisTernak');

        if (!mapEl || !dataEl) return;

        const rows = JSON.parse(dataEl.textContent || '[]');
        const geojsonUrl = mapEl.dataset.geojson;
        const fmt = new Intl.NumberFormat('id-ID');

        const norm = (value) => String(value || '')
            .replace(/^Kabupaten\s+/i, '')
            .replace(/^Kota\s+/i, '')
            .trim()
            .toLowerCase()
            .replace(/^bireun$/, 'bireuen');

        const byWilayah = new Map();

        rows.forEach((row) => {
            const key = norm(row.wilayah);
            const jumlah = Number(row.jumlah) || 0;

            if (!byWilayah.has(key)) {
                byWilayah.set(key, {
                    total: 0,
                    jenis: new Map(),
                });
            }

            const item = byWilayah.get(key);
            item.total += jumlah;
            item.jenis.set(row.jenis, (item.jenis.get(row.jenis) || 0) + jumlah);
        });

        let currentJenis = filterEl ? filterEl.value : '';
        let maxValue = 1;
        let geoLayer = null;

        const map = L.map(mapEl, {
            scrollWheelZoom: false,
            zoomSnap: 0.1,
        }).setView([4.7, 96.8], 8);

        L.tileLayer(
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            {
                attribution: 'Tiles &copy; Esri',
                maxZoom: 19,
            }
        ).addTo(map);

        function valueFor(feature) {
            const item = byWilayah.get(norm(feature.properties.nama));

            if (!item) return 0;

            return currentJenis
                ? (item.jenis.get(currentJenis) || 0)
                : item.total;
        }

        function computeMaxValue() {
            let max = 0;

            byWilayah.forEach((item) => {
                const value = currentJenis
                    ? (item.jenis.get(currentJenis) || 0)
                    : item.total;

                if (value > max) max = value;
            });

            maxValue = max || 1;
        }

        function colorFor(value) {
            if (!value) return '#d1d5db';

            const ratio = value / maxValue;

            if (ratio > 0.8) return '#7f1d1d';
            if (ratio > 0.6) return '#dc2626';
            if (ratio > 0.4) return '#f97316';
            if (ratio > 0.2) return '#fbbf24';

            return '#fde68a';
        }

        function styleFor(feature) {
            return {
                color: 'rgba(255, 255, 255, .9)',
                weight: 1.2,
                fillColor: colorFor(valueFor(feature)),
                fillOpacity: 0.7,
            };
        }

        function tooltipFor(feature) {
            const nama = feature.properties.nama || '-';
            const value = valueFor(feature);
            const label = currentJenis || 'Semua ternak';

            return `<strong>${nama}</strong><br>${label}: ${fmt.format(value)} ekor`;
        }

        const legend = L.control({ position: 'bottomright' });

        legend.onAdd = function () {
            this._div = L.DomUtil.create('div', 'ternak-map-legend');
            this._div.id = 'ternakMapLegend';
            return this._div;
        };

        legend.addTo(map);

        function updateLegend() {
            const div = document.getElementById('ternakMapLegend');
            if (!div) return;

            const items = [
                [0.8, '#7f1d1d'],
                [0.6, '#dc2626'],
                [0.4, '#f97316'],
                [0.2, '#fbbf24'],
                [0, '#fde68a'],
            ].map(([ratio, color]) => `
                <div class="ternak-map-legend-item">
                    <span class="ternak-map-swatch" style="background:${color}"></span>
                    ${fmt.format(Math.round(maxValue * ratio))} ekor
                </div>
            `).join('');

            div.innerHTML = `
                <div class="ternak-map-legend-title">
                    ${currentJenis || 'Semua Ternak'} (ekor)
                </div>
                ${items}
            `;
        }

        function refreshMap() {
            if (!geoLayer) return;

            computeMaxValue();

            geoLayer.eachLayer((layer) => {
                layer.setStyle(styleFor(layer.feature));
                layer.setTooltipContent(tooltipFor(layer.feature));
            });

            updateLegend();
        }

        if (!geojsonUrl) {
            console.warn('URL GeoJSON belum diatur pada elemen #map.');
            return;
        }

        fetch(geojsonUrl)
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Gagal memuat GeoJSON');
                }

                return response.json();
            })
            .then((geojson) => {
                geoLayer = L.geoJSON(geojson, {
                    style: styleFor,

                    onEachFeature: (feature, layer) => {
                        layer.bindTooltip(tooltipFor(feature), {
                            sticky: true,
                            className: 'ternak-map-tip',
                        });
                    },
                }).addTo(map);

                map.fitBounds(geoLayer.getBounds(), {
                    padding: [10, 10],
                });

                refreshMap();
            })
            .catch((error) => {
                console.error('Peta populasi ternak gagal dimuat:', error);
            });

        if (filterEl) {
            filterEl.addEventListener('change', function () {
                currentJenis = this.value;
                refreshMap();
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();