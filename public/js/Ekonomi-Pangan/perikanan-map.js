(function () {
    'use strict';

    function init() {
        if (typeof L === 'undefined') {
            console.warn('Leaflet belum termuat. Peta produksi perikanan tidak diinisialisasi.');
            return;
        }

        const mapEl = document.getElementById('map');
        const dataEl = document.getElementById('data-produksi-perikanan');
        const jenisEl = document.getElementById('selJenisPerikanan');
        const tahunEl = document.getElementById('selTahun');

        if (!mapEl || !dataEl || !jenisEl || !tahunEl) return;

        const data = JSON.parse(dataEl.textContent || '{}');
        const geojsonUrl = mapEl.dataset.geojson;
        const fmt = new Intl.NumberFormat('id-ID');

        const norm = (value) => String(value || '')
            .replace(/^Kabupaten\s+/i, '')
            .replace(/^Kota\s+/i, '')
            .trim()
            .toLowerCase()
            .replace(/^bireun$/, 'bireuen');

        let byWilayah = new Map();
        let maxValue = 1;
        let hasData = false;
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

        function currentLabel() {
            return jenisEl.options[jenisEl.selectedIndex]?.text || jenisEl.value;
        }

        function currentRows() {
            return data[jenisEl.value]?.[tahunEl.value] || [];
        }

        function computeData() {
            byWilayah = new Map();
            maxValue = 0;
            hasData = false;

            currentRows().forEach((row) => {
                const value = Number(row.volume) || 0;

                byWilayah.set(norm(row.wilayah), value);

                if (value > 0) hasData = true;
                if (value > maxValue) maxValue = value;
            });

            maxValue = maxValue || 1;
        }

        function valueFor(feature) {
            return byWilayah.get(norm(feature.properties.nama)) || 0;
        }

        function colorFor(value) {
            if (!value) return '#d1d5db';

            const ratio = value / maxValue;

            if (ratio > 0.8) return '#0c4a6e';
            if (ratio > 0.6) return '#0369a1';
            if (ratio > 0.4) return '#0ea5e9';
            if (ratio > 0.2) return '#7dd3fc';

            return '#bae6fd';
        }

        function styleFor(feature) {
            return {
                color: 'rgba(255, 255, 255, .9)',
                weight: 1.2,
                fillColor: colorFor(valueFor(feature)),
                fillOpacity: 0.75,
            };
        }

        function tooltipFor(feature) {
            const nama = feature.properties.nama || '-';
            const value = valueFor(feature);

            return `<strong>${nama}</strong><br>` +
                `${currentLabel()} ${tahunEl.value}: ` +
                `${fmt.format(Math.round(value))} ton`;
        }

        const legend = L.control({ position: 'bottomright' });

        legend.onAdd = function () {
            this._div = L.DomUtil.create('div', 'perikanan-map-legend');
            this._div.id = 'perikananMapLegend';
            return this._div;
        };

        legend.addTo(map);

        function updateLegend() {
            const div = document.getElementById('perikananMapLegend');
            if (!div) return;

            const title = `${currentLabel()} (${tahunEl.value}) - ton`;

            if (!hasData) {
                div.innerHTML = `
                    <div class="perikanan-map-legend-title">${title}</div>
                    <div class="perikanan-map-legend-empty">Tidak ada data</div>
                `;
                return;
            }

            const items = [
                [0.8, '#0c4a6e'],
                [0.6, '#0369a1'],
                [0.4, '#0ea5e9'],
                [0.2, '#7dd3fc'],
                [0, '#bae6fd'],
            ].map(([ratio, color]) => `
                <div class="perikanan-map-legend-item">
                    <span class="perikanan-map-swatch" style="background:${color}"></span>
                    ${fmt.format(Math.round(maxValue * ratio))} ton
                </div>
            `).join('');

            div.innerHTML = `
                <div class="perikanan-map-legend-title">${title}</div>
                ${items}
            `;
        }

        function refreshMap() {
            computeData();

            if (!geoLayer) {
                updateLegend();
                return;
            }

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
                            className: 'perikanan-map-tip',
                        });
                    },
                }).addTo(map);

                map.fitBounds(geoLayer.getBounds(), {
                    padding: [10, 10],
                });

                refreshMap();
            })
            .catch((error) => {
                console.error('Peta produksi perikanan gagal dimuat:', error);
            });

        jenisEl.addEventListener('change', refreshMap);
        tahunEl.addEventListener('change', refreshMap);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();