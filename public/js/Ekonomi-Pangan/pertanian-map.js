(function () {
    'use strict';

    function init() {
        if (typeof L === 'undefined') {
            console.warn('Leaflet belum termuat. Peta luas lahan tidak diinisialisasi.');
            return;
        }

        const mapEl = document.getElementById('map');
        const dataEl = document.getElementById('data-luas-lahan');
        const filterEl = document.getElementById('filterJenisLahan');

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
            const luas = Number(row.luas) || 0;

            if (!byWilayah.has(key)) {
                byWilayah.set(key, {
                    total: 0,
                    jenis: new Map(),
                });
            }

            const item = byWilayah.get(key);
            item.total += luas;
            item.jenis.set(row.jenis, (item.jenis.get(row.jenis) || 0) + luas);
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

        function valueFor(props) {
            const item = byWilayah.get(norm(props.nama));

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

            if (ratio > 0.8) return '#08519c';
            if (ratio > 0.6) return '#3182bd';
            if (ratio > 0.4) return '#6baed6';
            if (ratio > 0.2) return '#9ecae1';

            return '#c6dbef';
        }

        function styleFor(feature) {
            return {
                color: 'rgba(255, 255, 255, .9)',
                weight: 1.2,
                fillColor: colorFor(valueFor(feature.properties)),
                fillOpacity: 0.7,
            };
        }

        function tooltipFor(feature) {
            const nama = feature.properties.nama || '-';
            const value = valueFor(feature.properties);
            const label = currentJenis || 'Semua jenis lahan';

            return `<strong>${nama}</strong><br>${label}: ${fmt.format(value)} ha`;
        }

        const legend = L.control({ position: 'bottomright' });

        legend.onAdd = function () {
            this._div = L.DomUtil.create('div', 'lahan-map-legend');
            this._div.id = 'lahanMapLegend';
            return this._div;
        };

        legend.addTo(map);

        function updateLegend() {
            const div = document.getElementById('lahanMapLegend');
            if (!div) return;

            const items = [
                [0.8, '#08519c'],
                [0.6, '#3182bd'],
                [0.4, '#6baed6'],
                [0.2, '#9ecae1'],
                [0, '#c6dbef'],
            ].map(([ratio, color]) => `
                <div class="lahan-map-legend-item">
                    <span class="lahan-map-swatch" style="background:${color}"></span>
                    ${fmt.format(Math.round(maxValue * ratio))} ha
                </div>
            `).join('');

            div.innerHTML = `
                <div class="lahan-map-legend-title">
                    ${currentJenis || 'Semua Jenis Lahan'} (ha)
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
                            className: 'lahan-map-tip',
                        });
                    },
                }).addTo(map);

                map.fitBounds(geoLayer.getBounds(), {
                    padding: [10, 10],
                });

                map.setZoom(map.getZoom());

                refreshMap();
            })
            .catch((error) => {
                console.error('Peta luas lahan gagal dimuat:', error);
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