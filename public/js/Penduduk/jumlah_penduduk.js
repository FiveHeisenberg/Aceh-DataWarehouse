/**
 * Aceh Data Warehouse - Jumlah Penduduk Module
 * File: public/js/penduduk/jumlah_penduduk.js
 * 
 * Mengelola interaksi dashboard jumlah penduduk dengan API 
 * (Termasuk: Summary, Tabel, Chart Tren, dan Peta Choropleth)
 */
(function() {
    'use strict';

    // ==================== KONFIGURASI ====================
    const CONFIG = {
        API_BASE_URL: '/api/penduduk',
        DEFAULT_PER_PAGE: 25,
        DEBOUNCE_DELAY: 300
    };

    // ==================== STATE ====================
    let state = {
        currentYear: null,
        years: [],
        details: [],
        summary: null,
        trendData: [],
        searchKeyword: ''
    };

    // State khusus untuk Peta
    let mapState = {
        geoLayer: null,
        popByKode: new Map(),
        popByName: new Map(),
        currentTahun: null
    };

    // ==================== DOM ELEMENTS ====================
    const elements = {};

    function cacheElements() {
        elements.yearSelect = document.getElementById('filter-tahun');
        elements.statTotal = document.getElementById('stat-total');
        elements.statTotalSatuan = document.getElementById('stat-total-satuan');
        elements.statPertumbuhan = document.getElementById('stat-pertumbuhan');
        elements.tableBody = document.getElementById('table-body');
        elements.tableNote = document.getElementById('table-note');
        elements.searchInput = document.getElementById('table-search');
        elements.trendChart = document.getElementById('trendChart');
        elements.filterKabupaten = document.getElementById('filter-kabupaten');
        elements.acehMap = document.getElementById('aceh-map'); // Elemen Peta
    }

    // ==================== UTILITY FUNCTIONS ====================
    
    function formatNumber(num) {
        if (!num && num !== 0) return '—';
        return num.toLocaleString('id-ID');
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function showLoading(element, message = 'Memuat data...') {
        if (element) {
            element.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-muted">${message}</td></tr>`;
        }
    }

    function showError(element, message = 'Gagal memuat data') {
        if (element) {
            element.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-danger">${message}</td></tr>`;
        }
    }

    // ==================== API FUNCTIONS ====================

    async function fetchYears() {
        try {
            const response = await fetch(`${CONFIG.API_BASE_URL}/years`);
            const result = await response.json();
            if (result.success) {
                state.years = result.data;
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error fetch years:', error);
            return false;
        }
    }

    async function fetchIndexData(tahun, search = '') {
        try {
            let url = `${CONFIG.API_BASE_URL}/index?tahun=${tahun}&per_page=${CONFIG.DEFAULT_PER_PAGE}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;

            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
                state.currentYear = result.data.tahun_aktif;
                state.summary = result.data.summary;
                state.details = result.data.details.data;
                state.trendData = result.data.tren;
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error fetch index:', error);
            return false;
        }
    }

    // ==================== MAP FUNCTIONS ====================

    // Normalisasi untuk pencocokan nama/kode yang toleran
    const normKode = (k) => String(k || '').replace(/\D/g, '');
    const normName = (n) => String(n || '')
        .toUpperCase()
        .replace(/^KABUPATEN\s+/, '')
        .replace(/^KAB.?\s+/, '')
        .replace(/^KOTA\s+/, '')
        .replace(/[^A-Z]/g, '');

    function lookupMapData(props) {
        return mapState.popByName.get(normName(props.nama)) || mapState.popByKode.get(normKode(props.kode)) || null;
    }

    function getColor(pop) {
        return pop > 500000 ? '#7f0000' :
               pop > 400000 ? '#b30000' :
               pop > 300000 ? '#d7301f' :
               pop > 200000 ? '#ef6548' :
               pop > 0       ? '#fcbba1' : '#e3e7ee';
    }

    function styleFeature(feature) {
        const d = lookupMapData(feature.properties);
        return {
            color: '#ffffff',
            weight: 1,
            fillColor: getColor(d ? d.jumlah_penduduk : 0),
            fillOpacity: 0.8,
        };
    }

    function tooltipHtml(props) {
        const d = lookupMapData(props);
        if (!d) {
            return `<strong>${props.nama}</strong><br>Tidak ada data untuk tahun ${mapState.currentTahun ?? '-'}`;
        }
        const growth = (d.pertumbuhan_persen === null || d.pertumbuhan_persen === undefined)
            ? ''
            : `<br>Pertumbuhan: ${d.pertumbuhan_persen > 0 ? '+' : ''}${d.pertumbuhan_persen}%`;
            
        return `<strong>${d.nama}</strong><br>
                Tahun ${mapState.currentTahun}: ${formatNumber(d.jumlah_penduduk)} ${(d.satuan || 'jiwa').toLowerCase()}<br>
                Peringkat ${d.peringkat} dari ${mapState.popByKode.size}${growth}`;
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
                if (mapState.geoLayer) mapState.geoLayer.resetStyle(e.target);
            },
            click: () => {
                const d = lookupMapData(feature.properties);
                if (elements.filterKabupaten && d) {
                    elements.filterKabupaten.value = d.kode;
                    elements.filterKabupaten.dispatchEvent(new Event('change'));
                }
            },
        });
    }

    async function loadPopulationForYearMap(tahun) {
        try {
            const url = `${CONFIG.API_BASE_URL}/map${tahun ? '?tahun=' + tahun : ''}`;
            const res = await fetch(url);
            const body = await res.json();
            
            if (!body.success) throw new Error(body.message || 'Gagal memuat data peta');
            
            mapState.currentTahun = body.data.tahun;
            mapState.popByKode = new Map();
            mapState.popByName = new Map();
            
            body.data.kabupaten.forEach((row) => {
                mapState.popByKode.set(normKode(row.kode), row);
                mapState.popByName.set(normName(row.nama), row);
            });
            
            if (mapState.geoLayer) {
                mapState.geoLayer.setStyle(styleFeature);
            }
        } catch (err) {
            console.error('Gagal memuat data peta:', err);
        }
    }

    async function initMap() {
        if (!elements.acehMap) return;
        if (typeof L === 'undefined') {
            console.warn('Leaflet belum termuat. Peta tidak dapat diinisialisasi.');
            return;
        }

        const map = L.map(elements.acehMap, { scrollWheelZoom: false }).setView([4.7, 96.8], 8);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 20,
        }).addTo(map);

        try {
            // Ambil URL dari meta tag atau fallback ke path default
            const geojsonUrl = document.querySelector('meta[name="geojson-url"]')?.content || '/assets/data/aceh-kabupaten.geojson';
            const geojson = await fetch(geojsonUrl).then((r) => r.json());
            
            // Load data tahun terbaru saat inisialisasi
            await loadPopulationForYearMap(null); 
            
            mapState.geoLayer = L.geoJSON(geojson, { 
                style: styleFeature, 
                onEachFeature 
            }).addTo(map);
            
            map.fitBounds(mapState.geoLayer.getBounds(), { padding: [12, 12] });
            
        } catch (err) {
            console.error('Gagal memuat peta GeoJSON:', err);
        }
    }

    // ==================== RENDER FUNCTIONS ====================

    function renderYearDropdown() {
        if (!elements.yearSelect || state.years.length === 0) return;
        elements.yearSelect.innerHTML = '';
        
        state.years.forEach(year => {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            if (year === state.years[0]) {
                option.selected = true;
                state.currentYear = year;
            }
            elements.yearSelect.appendChild(option);
        });
    }

    function renderSummary() {
        if (!state.summary) return;
        if (elements.statTotal) elements.statTotal.textContent = formatNumber(state.summary.total_penduduk);
        if (elements.statTotalSatuan) elements.statTotalSatuan.textContent = `dalam jiwa, tahun ${state.currentYear}`;
        if (elements.statPertumbuhan) {
            const pertumbuhan = state.summary.pertumbuhan_persen;
            const prefix = pertumbuhan > 0 ? '+' : '';
            elements.statPertumbuhan.textContent = `${prefix}${pertumbuhan}%`;
        }
    }

    function renderTable() {
        if (!elements.tableBody) return;
        if (!state.details || state.details.length === 0) {
            elements.tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada data ditemukan</td></tr>`;
            return;
        }
        elements.tableBody.innerHTML = '';
        state.details.forEach((item) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-4 py-3">${item.nama_kabupaten_kota}</td>
                <td class="px-4 py-3 text-end">${item.tahun}</td>
                <td class="px-4 py-3 text-end fw-semibold">${formatNumber(item.jumlah_penduduk)}</td>
                <td class="px-4 py-3 text-end text-muted">${item.satuan || 'jiwa'}</td>
            `;
            elements.tableBody.appendChild(row);
        });
        if (elements.tableNote) elements.tableNote.textContent = `${state.details.length} baris`;
    }

    function renderTrendChart() {
        if (!elements.trendChart || !state.trendData || state.trendData.length === 0) return;
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js tidak tersedia');
            return;
        }
        const ctx = elements.trendChart.getContext('2d');
        if (window.trendChartInstance) window.trendChartInstance.destroy();

        const labels = state.trendData.map(item => item.tahun);
        const data = state.trendData.map(item => item.total);

        window.trendChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Penduduk',
                    data: data,
                    borderColor: '#0d9488',
                    backgroundColor: 'rgba(13, 148, 136, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#0d9488',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1a2e',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Jumlah: ' + formatNumber(context.parsed.y) + ' jiwa';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) { return (value / 1000000).toFixed(2) + 'M'; },
                            font: { size: 11 },
                            color: '#8892a4'
                        },
                        grid: { color: '#e0e4f0', drawBorder: false }
                    },
                    x: {
                        ticks: { font: { size: 12 }, color: '#5a6577' },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // ==================== MAIN FUNCTIONS ====================

    async function loadInitialData() {
        const yearsLoaded = await fetchYears();
        if (!yearsLoaded) {
            showError(elements.tableBody, 'Gagal memuat daftar tahun');
            return;
        }
        renderYearDropdown();
        await loadData(state.currentYear);
    }

    async function loadData(tahun, search = '') {
        showLoading(elements.tableBody, 'Memuat data...');
        const success = await fetchIndexData(tahun, search);
        if (success) {
            renderSummary();
            renderTable();
            renderTrendChart();
        } else {
            showError(elements.tableBody, 'Gagal memuat data dari server');
        }
    }

    function handleYearChange(event) {
        const selectedYear = parseInt(event.target.value);
        if (selectedYear) {
            state.currentYear = selectedYear;
            loadData(selectedYear, state.searchKeyword);
            loadPopulationForYearMap(selectedYear); // Update peta secara sinkron
        }
    }

    const handleSearch = debounce(function(event) {
        state.searchKeyword = event.target.value.trim();
        if (state.currentYear) {
            loadData(state.currentYear, state.searchKeyword);
        }
    }, CONFIG.DEBOUNCE_DELAY);

    // ==================== EVENT LISTENERS ====================

    function attachEventListeners() {
        if (elements.yearSelect) {
            elements.yearSelect.addEventListener('change', handleYearChange);
        }
        if (elements.searchInput) {
            elements.searchInput.addEventListener('input', handleSearch);
        }
    }

    // ==================== INITIALIZATION ====================

    function init() {
        console.log('%c Aceh Data Warehouse - Jumlah Penduduk ', 'background: #0d9488; color: #fff; font-size: 12px; padding: 4px 8px; border-radius: 4px;');
        cacheElements();
        attachEventListeners();
        
        // Inisialisasi Peta
        initMap();
        
        // Load data awal (Tabel, Chart, Summary)
        loadInitialData();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();