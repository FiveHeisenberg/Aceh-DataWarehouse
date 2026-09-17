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
        kabupaten: [],
        details: [],
        summary: null,
        trendData: [],
        searchKeyword: ''
    };

    // ==================== DOC ELEMENTS ====================
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
        elements.filterTrend = document.getElementById('filter-trend');
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
            element.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-muted">${message}</td></tr>`;
        }
    }

    function showError(element, message = 'Gagal memuat data') {
        if (element) {
            element.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-danger">${message}</td></tr>`;
        }
    }

    // ==================== API FUNCTIONS ====================

    async function fetchYears() {
        try {
            const response = await fetch(`${CONFIG.API_BASE_URL}/tahun`);
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
            const response = await fetch(`${CONFIG.API_BASE_URL}/jumlah-penduduk`);
            const result = await response.json();

            if (result.success) {
                const rows = result.data;
                const current = rows.find(r => Number(r.tahun) === Number(tahun));
                const prev = rows.find(r => Number(r.tahun) === Number(tahun) - 1);

                state.currentYear = tahun;
                state.summary = {
                    total_penduduk: current ? Number(current.jumlah) : 0,
                    total_tahun_lalu: prev ? Number(prev.jumlah) : 0,
                    pertumbuhan_persen: (current && prev && prev.jumlah > 0)
                        ? ((current.jumlah - prev.jumlah) / prev.jumlah) * 100
                        : 0,
                };
                state.details = [];
                state.trendData = rows.map(r => ({ tahun: Number(r.tahun), total: Number(r.jumlah) }));
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error fetch data:', error);
            return false;
        }
    }

    async function fetchKabupatenOptions() {
        try {
            const response = await fetch(`${CONFIG.API_BASE_URL}/trend-pertumbuhan`);
            const result = await response.json();
            if (result.success) {
                state.kabupaten = result.data.kabupaten;
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error fetch kabupaten:', error);
            return false;
        }
    }

    async function fetchTrendPerKabupaten(nama) {
        try {
            const response = await fetch(`${CONFIG.API_BASE_URL}/trend-pertumbuhan?wilayah=${encodeURIComponent(nama)}`);
            const result = await response.json();
            if (result.success) {
                state.trendData = result.data.tren.map(r => ({ tahun: r.tahun, total: r.jumlah }));
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error fetch tren kabupaten:', error);
            return false;
        }
    }

    async function fetchTableData(tahun, search = '') {
        try {
            let url = `${CONFIG.API_BASE_URL}/detail-penduduk`;
            const params = new URLSearchParams();
            if (tahun) params.set('tahun', tahun);
            if (search) params.set('search', search);
            const qs = params.toString();
            if (qs) url += `?${qs}`;

            const response = await fetch(url);
            const result = await response.json();
            if (result.success) {
                state.details = result.data;
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error fetch detail:', error);
            return false;
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

    function renderTrendSelect() {
        if (!elements.filterTrend || !state.kabupaten || state.kabupaten.length === 0) return;
        elements.filterTrend.innerHTML = '<option value="">Seluruh Aceh (total)</option>';

        state.kabupaten.forEach((kab) => {
            const option = document.createElement('option');
            option.value = kab.nama;
            option.textContent = kab.nama;
            elements.filterTrend.appendChild(option);
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
            elements.tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-muted">Tidak ada data ditemukan</td></tr>`;
            return;
        }
        elements.tableBody.innerHTML = '';
        state.details.forEach((item) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-4 py-3">${item.nama_kabupaten_kota}</td>
                <td class="px-4 py-3 text-end">${item.tahun}</td>
                <td class="px-4 py-3 text-end fw-semibold">${formatNumber(item.jumlah_penduduk)}</td>
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

        const nilaiMax = Math.max(...data, 0);

        let skala = 1, awalan = '';
        if (nilaiMax >= 1e9)      { skala = 1e9; awalan = 'B'; }
        else if (nilaiMax >= 1e6) { skala = 1e6; awalan = 'M'; }
        else if (nilaiMax >= 1e3) { skala = 1e3; awalan = 'K'; }

        function formatTick(v) {
            const hasil = v / skala;
            if (skala === 1) return v.toLocaleString('id-ID');
            return (hasil >= 100 ? Math.round(hasil).toString()
                : hasil.toFixed(hasil % 1 === 0 ? 0 : 1)) + awalan;
        }

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
                        beginAtZero: true,
                        suggestedMax: Math.ceil((nilaiMax * 1.2) / skala) * skala,
                        ticks: {
                            callback: formatTick,
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
        await fetchKabupatenOptions();
        renderTrendSelect();
    }

    async function loadData(tahun, search = '') {
        showLoading(elements.tableBody, 'Memuat data...');
        const success = await fetchIndexData(tahun, search);
        if (success) {
            renderSummary();
            renderTrendChart();
        } else {
            showError(elements.tableBody, 'Gagal memuat data dari server');
            return;
        }

        const tableOk = await fetchTableData(tahun, search);
        if (tableOk) {
            renderTable();
        } else {
            showError(elements.tableBody, 'Gagal memuat tabel');
        }
    }

    function handleYearChange(event) {
        const selectedYear = parseInt(event.target.value);
        if (selectedYear) {
            state.currentYear = selectedYear;
            loadData(selectedYear, state.searchKeyword);
        }
    }

    function handleTrendFilterChange(event) {
        const nama = event.target.value;

        if (!nama) {
            loadData(state.currentYear, state.searchKeyword);
            return;
        }

        fetchTrendPerKabupaten(nama).then((ok) => {
            if (ok) {
                renderTrendChart();
            } else {
                console.error('Gagal memuat tren untuk:', nama);
            }
        });
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
        if (elements.filterTrend) {
            elements.filterTrend.addEventListener('change', handleTrendFilterChange);
        }
    }

    // ==================== INITIALIZATION ====================

    function init() {
        console.log('%c Aceh Data Warehouse - Jumlah Penduduk ', 'background: #0d9488; color: #fff; font-size: 12px; padding: 4px 8px; border-radius: 4px;');
        cacheElements();
        attachEventListeners();
        
        // Load data awal (Tabel, Chart, Summary)
        loadInitialData();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();