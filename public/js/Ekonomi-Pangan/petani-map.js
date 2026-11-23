(function () {
    'use strict';

    function init() {
        if (typeof L === 'undefined') return;

        const mapEl = document.getElementById('petaPetani');
        const dataEl = document.getElementById('data-peta-petani');

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

        const byWilayah = new Map(
            rows.map((row) => [norm(row.wilayah), Number(row.jumlah) || 0])
        );

        const nilaiMaks = Math.max(0, ...byWilayah.values());
        const maxValue = nilaiMaks || 1;

        function colorFor(value) {
            if (!value) return '#d1d5db';
            const ratio = value / maxValue;
            if (ratio > 0.8) return '#14532d';
            if (ratio > 0.6) return '#166534';
            if (ratio > 0.4) return '#16a34a';
            if (ratio > 0.2) return '#4ade80';
            return '#bbf7d0';
        }

        function valueFor(props) {
            return byWilayah.get(norm(props.nama)) || 0;
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
            return `<strong>${feature.properties.nama || '-'}</strong><br>Jumlah petani: ${fmt.format(valueFor(feature.properties))}`;
        }

        const map = L.map(mapEl, { scrollWheelZoom: false, zoomSnap: 0.1 })
            .setView([4.7, 96.8], 8);

        L.tileLayer(
            'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            { attribution: 'Tiles &copy; Esri', maxZoom: 19 }
        ).addTo(map);

        const legend = L.control({ position: 'bottomright' });

        legend.onAdd = function () {
            const div = L.DomUtil.create('div', 'lahan-map-legend');
            div.id = 'petaniMapLegend';
            return div;
        };
        legend.addTo(map);

        function updateLegend() {
            const div = document.getElementById('petaniMapLegend');
            if (!div) return;

            div.innerHTML = !nilaiMaks
                ? '<div class="lahan-map-legend-title">Belum ada data petani</div>'
                : `
                    <div class="lahan-map-legend-title">Jumlah Petani (orang)</div>
                    ${[
                        [0.8, '#14532d'],
                        [0.6, '#166534'],
                        [0.4, '#16a34a'],
                        [0.2, '#4ade80'],
                        [0, '#bbf7d0'],
                    ].map(([ratio, color]) => `
                        <div class="lahan-map-legend-item">
                            <span class="lahan-map-swatch" style="background:${color}"></span>
                            ${fmt.format(Math.round(maxValue * ratio))}
                        </div>
                    `).join('')}
                `;
        }

        updateLegend();

        if (!geojsonUrl) return;

        fetch(geojsonUrl)
            .then((response) => {
                if (!response.ok) throw new Error('Gagal memuat GeoJSON');
                return response.json();
            })
            .then((geojson) => {
                const geoLayer = L.geoJSON(geojson, {
                    style: styleFor,
                    onEachFeature: (feature, layer) => {
                        layer.bindTooltip(tooltipFor(feature), {
                            sticky: true,
                            className: 'lahan-map-tip',
                        });
                    },
                }).addTo(map);

                map.fitBounds(geoLayer.getBounds(), { padding: [10, 10] });
                map.setZoom(map.getZoom());
            })
            .catch((error) => console.error('Peta jumlah petani gagal dimuat:', error));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
