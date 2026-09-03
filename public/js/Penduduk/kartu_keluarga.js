/**
 * Aceh Data Warehouse - Kartu Keluarga Module
 * File: public/js/penduduk/kartu_keluarga.js
 * 
 * Mengelola dashboard Kartu Keluarga dengan API integration
 */
(function() {
    'use strict';

    // ==================== KONFIGURASI ====================
    const CONFIG = {
        API_BASE_URL: '/api/penduduk/kk',
        DEFAULT_PER_PAGE: 25,
        DEBOUNCE_DELAY: 300,
        CHART_COLOR: '#0d9488',
        CHART_BG_COLOR: 'rgba(13, 148, 136, 0.1)'
    };

    // ==================== STATE ====================
    let state = {
        currentYear: null,
        years: [],
        details: [],
        summary: null,
        trendData: [],
        kabTertinggi: null,
        kotaTercepat: null,
        searchKeyword: ''
    };

    // ==================== DOM ELEMENTS ====================
    const elements = {};

    function cacheElements() {
        elements.yearSelect = document.getElementById('filter-tahun');
        elements.statTotalKK = document.getElementById('stat-total-kk');
        elements.statKKYearBadge = document.getElementById('stat-kk-year-badge');       // BARU
        elements.statKKGrowth = document.getElementById('stat-kk-growth');              // BARU
        elements.statKKGrowthIcon = document.getElementById('stat-kk-growth-icon');     // BARU
        elements.statKKGrowthValue = document.getElementById('stat-kk-growth-value');   // BARU
        elements.tableBody = document.getElementById('kk-table-body');
        elements.tableNote = document.getElementById('kk-table-note');
        elements.searchInput = document.getElementById('kk-search');
        elements.trenChart = document.getElementById('trenKKChart');
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

    /**
     * Fetch daftar tahun dari API
     */
    async function fetchYears() {
        try {
            const response = await fetch(`${CONFIG.API_BASE_URL}/years`);
            const result = await response.json();

            if (result.success) {
                state.years = result.data;
                return true;
            } else {
                console.error('Gagal mengambil tahun:', result.message);
                return false;
            }
        } catch (error) {
            console.error('Error fetch years:', error);
            return false;
        }
    }

    /**
     * Fetch data index KK (summary + details + trend)
     */
    async function fetchKKIndex(tahun, search = '') {
        try {
            let url = `${CONFIG.API_BASE_URL}/index?tahun=${tahun}&per_page=${CONFIG.DEFAULT_PER_PAGE}`;
            if (search) {
                url += `&search=${encodeURIComponent(search)}`;
            }

            const response = await fetch(url);
            const result = await response.json();

            if (result.success) {
                state.currentYear = result.data.tahun_aktif;
                state.summary = result.data.summary;
                state.details = result.data.details.data;
                state.trendData = result.data.tren;
                state.kabTertinggi = result.data.kab_tertinggi;
                state.kotaTercepat = result.data.kota_tercepat;
                return true;
            } else {
                console.error('Gagal mengambil data KK:', result.message);
                return false;
            }
        } catch (error) {
            console.error('Error fetch KK index:', error);
            return false;
        }
    }

    // ==================== RENDER FUNCTIONS ====================

    /**
     * Render dropdown tahun
     */
    function renderYearDropdown() {
        if (!elements.yearSelect || state.years.length === 0) return;

        elements.yearSelect.innerHTML = '';

        state.years.forEach(year => {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;

            // Set tahun terbaru sebagai default
            if (year === state.years[0]) {
                option.selected = true;
                state.currentYear = year;
            }

            elements.yearSelect.appendChild(option);
        });
    }

    /**
     * Render summary cards
     */
/**
 * Render summary cards (Total KK, Tahun, Pertumbuhan)
 */
    function renderSummaryCards() {
        if (!state.summary) return;

        // 1. Update badge tahun (contoh: "Aceh 2024")
        if (elements.statKKYearBadge) {
            elements.statKKYearBadge.textContent = `Aceh ${state.currentYear}`;
        }

        // 2. Update total KK (contoh: "1.412.850")
        if (elements.statTotalKK) {
            elements.statTotalKK.textContent = formatNumber(state.summary.total_kk);
        }

        // 3. Update persentase pertumbuhan
        const pertumbuhan = state.summary.pertumbuhan_persen || 0;
        const isNaik = pertumbuhan >= 0;
        const prefix = isNaik ? '+' : '';
        const formattedGrowth = `${prefix}${pertumbuhan.toFixed(1).replace('.', ',')}%`;

        // Update nilai teks
        if (elements.statKKGrowthValue) {
            elements.statKKGrowthValue.textContent = formattedGrowth;
        }

        // Update ikon panah (naik/turun)
        if (elements.statKKGrowthIcon) {
            if (isNaik) {
                elements.statKKGrowthIcon.className = 'bi bi-arrow-up-short me-1';
                elements.statKKGrowthIcon.style.fontSize = '16px';
            } else {
                elements.statKKGrowthIcon.className = 'bi bi-arrow-down-short me-1';
                elements.statKKGrowthIcon.style.fontSize = '16px';
            }
        }

        // Update warna badge (hijau untuk naik, merah untuk turun)
        if (elements.statKKGrowth) {
            if (isNaik) {
                elements.statKKGrowth.style.backgroundColor = '#e8f5f0';
                elements.statKKGrowth.style.color = '#0d9488';
            } else {
                elements.statKKGrowth.style.backgroundColor = '#fde8e8';
                elements.statKKGrowth.style.color = '#dc2626';
            }
        }

        console.log('Summary cards rendered:', state.summary);
    }

    /**
     * Render tabel detail KK
     */
    function renderTable() {
        if (!elements.tableBody) return;

        if (!state.details || state.details.length === 0) {
            elements.tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                        Tidak ada data ditemukan
                    </td>
                </tr>
            `;
            return;
        }

        elements.tableBody.innerHTML = '';

        state.details.forEach((item) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-4 py-3">
                    <i class="bi bi-${item.nama_kabupaten_kota.toLowerCase().includes('kota') ? 'building-fill' : 'building'} me-2" 
                       style="color: ${item.nama_kabupaten_kota.toLowerCase().includes('kota') ? '#0d9488' : '#5a6577'};"></i>
                    <strong style="color: #1a1a2e;">${item.nama_kabupaten_kota}</strong>
                </td>
                <td class="px-4 py-3 text-end text-muted">—</td>
                <td class="px-4 py-3 text-end text-muted">—</td>
                <td class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e;">
                    ${formatNumber(item.jumlah_kartu_keluarga)}
                </td>
            `;
            elements.tableBody.appendChild(row);
        });

        if (elements.tableNote) {
            elements.tableNote.textContent = `${state.details.length} baris`;
        }
    }

    /**
     * Render Chart Tren KK
     */
    function renderTrenChart() {
        if (!elements.trenChart || !state.trendData || state.trendData.length === 0) return;
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js tidak tersedia');
            return;
        }

        const ctx = elements.trenChart.getContext('2d');

        if (window.trenKKChartInstance) {
            window.trenKKChartInstance.destroy();
        }

        const labels = state.trendData.map(item => {
            return item.tahun === state.currentYear ? `${item.tahun} (Saat ini)` : item.tahun;
        });
        const data = state.trendData.map(item => item.total);

        window.trenKKChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah KK',
                    data: data,
                    borderColor: CONFIG.CHART_COLOR,
                    backgroundColor: CONFIG.CHART_BG_COLOR,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: CONFIG.CHART_COLOR,
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    fill: true,
                    tension: 0.3
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
                                return 'KK: ' + formatNumber(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return (value / 1000000).toFixed(2) + 'M';
                            },
                            font: { size: 11 },
                            color: '#8892a4'
                        },
                        grid: {
                            color: '#e8e8e8',
                            drawBorder: false,
                            borderDash: [5, 5]
                        }
                    },
                    x: {
                        ticks: {
                            font: { size: 12, weight: '600' },
                            color: function(context) {
                                return context.tick.label.includes('Saat ini') 
                                    ? CONFIG.CHART_COLOR 
                                    : '#5a6577';
                            }
                        },
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

        const success = await fetchKKIndex(tahun, search);
        if (success) {
            renderSummaryCards();
            renderTable();
            renderTrenChart();
        } else {
            showError(elements.tableBody, 'Gagal memuat data dari server');
        }
    }

    function handleYearChange(event) {
        const selectedYear = parseInt(event.target.value);
        if (selectedYear) {
            state.currentYear = selectedYear;
            loadData(selectedYear, state.searchKeyword);
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
        console.log('%c Aceh Data Warehouse - Kartu Keluarga ', 'background: #0d9488; color: #fff; font-size: 12px; padding: 4px 8px; border-radius: 4px;');
        
        cacheElements();
        attachEventListeners();
        loadInitialData();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose untuk debugging
    window.KartuKeluarga = {
        state: state,
        loadData: loadData,
        renderTrenChart: renderTrenChart
    };

})();