/**
 * Aceh Data Warehouse - Kartu Keluarga Module
 * File: public/js/Penduduk/kartu_keluarga.js
 *
 * Mengelola interaksi dashboard kartu keluarga dengan API
 * (Termasuk: Summary, Tabel, Chart Tren, dan Distribusi Wilayah)
 */
(function() {
    'use strict';

    // ==================== KONFIGURASI ====================
    const CONFIG = {
        API_BASE_URL: '/api/penduduk'
    };

    // ==================== STATE ====================
    let state = {
        currentYear: null,
        years: [],
        summary: null,
        trend: [],
        detail: [],
        search: ''
    };

    // ==================== DOC ELEMENTS ====================
    const elements = {};

    function cacheElements() {
        elements.yearSelect = document.getElementById('filter-tahun');
        elements.statYearBadge = document.getElementById('stat-kk-year-badge');
        elements.statTotal = document.getElementById('stat-total-kk');
        elements.statGrowth = document.getElementById('stat-kk-growth');
        elements.statGrowthIcon = document.getElementById('stat-kk-growth-icon');
        elements.statGrowthValue = document.getElementById('stat-kk-growth-value');
        elements.statTerbanyakNama = document.getElementById('stat-kk-terbanyak-nama');
        elements.statTerbanyakJumlah = document.getElementById('stat-kk-terbanyak-jumlah');
        elements.trenChart = document.getElementById('trenKKChart');
        elements.distribusiContainer = document.getElementById('distribusi-container');
        elements.distribusiSubtitle = document.getElementById('distribusi-subtitle');
        elements.searchInput = document.getElementById('kk-search');
        elements.tableBody = document.getElementById('kk-table-body');
        elements.tableYearHead = document.getElementById('kk-table-year-head');
        elements.showCount = document.getElementById('kk-show-count');
        elements.totalCount = document.getElementById('kk-total-count');
        elements.cakupanData = document.getElementById('cakupan-data');
        elements.tabelSubtitle = document.getElementById('tabel-subtitle');
    }

    // ==================== UTILITY ====================

    function formatNumber(num) {
        if (!num && num !== 0) return '—';
        return num.toLocaleString('id-ID');
    }

    function showTableLoading(message = 'Memuat data...') {
        if (elements.tableBody) {
            elements.tableBody.innerHTML = `<tr><td colspan="2" class="text-center py-4 text-muted">${message}</td></tr>`;
        }
    }

    function showTableError(message = 'Gagal memuat data') {
        if (elements.tableBody) {
            elements.tableBody.innerHTML = `<tr><td colspan="2" class="text-center py-4 text-danger">${message}</td></tr>`;
        }
    }

    // ==================== API ====================

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

    async function fetchSummary(tahun) {
        try {
            const url = `${CONFIG.API_BASE_URL}/kartu-keluarga/summary?tahun=${tahun}`;
            const response = await fetch(url);
            const result = await response.json();
            if (result.success) {
                state.summary = result.data;
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error fetch summary:', error);
            return false;
        }
    }

    async function fetchTrend() {
        try {
            const response = await fetch(`${CONFIG.API_BASE_URL}/kartu-keluarga/trend`);
            const result = await response.json();
            if (result.success) {
                state.trend = result.data;
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error fetch trend:', error);
            return false;
        }
    }

    async function fetchDetail(tahun) {
        try {
            const url = `${CONFIG.API_BASE_URL}/kartu-keluarga/detail?tahun=${tahun}`;
            const response = await fetch(url);
            const result = await response.json();
            if (result.success) {
                state.detail = result.data.detail || [];
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error fetch detail:', error);
            return false;
        }
    }

    // ==================== RENDER ====================

    function renderYearDropdown() {
        if (!elements.yearSelect || state.years.length === 0) return;
        elements.yearSelect.innerHTML = '';

        state.years.forEach(year => {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            elements.yearSelect.appendChild(option);
        });

        elements.yearSelect.value = state.years[state.years.length - 1];
        state.currentYear = Number(elements.yearSelect.value);
    }

    function renderSummary() {
        if (!state.summary) return;

        if (elements.statYearBadge) elements.statYearBadge.textContent = `Tahun ${state.summary.tahun}`;
        if (elements.statTotal) elements.statTotal.textContent = formatNumber(state.summary.total_kk);

        const pertumbuhan = state.summary.pertumbuhan_persen;

        if (elements.statGrowth && elements.statGrowthValue) {
            if (pertumbuhan === null || pertumbuhan === undefined) {
                elements.statGrowth.classList.add('d-none');
            } else {
                elements.statGrowth.classList.remove('d-none');
                const positif = pertumbuhan >= 0;
                const abs = Math.abs(pertumbuhan);

                elements.statGrowthValue.textContent = `${positif ? '+' : '-'}${abs.toLocaleString('id-ID', { maximumFractionDigits: 2 })}% vs thn lalu`;

                if (elements.statGrowthIcon) {
                    elements.statGrowthIcon.className = positif
                        ? 'bi bi-arrow-up-short me-1'
                        : 'bi bi-arrow-down-short me-1';
                }

                elements.statGrowth.style.backgroundColor = positif ? '#e8f5f0' : '#fdeaea';
                elements.statGrowth.style.color = positif ? '#0d9488' : '#dc2626';
            }
        }

        const terbanyak = state.summary.kabupaten_terbanyak;
        if (elements.statTerbanyakNama) {
            elements.statTerbanyakNama.textContent = terbanyak ? terbanyak.nama : '—';
        }
        if (elements.statTerbanyakJumlah) {
            elements.statTerbanyakJumlah.textContent = terbanyak ? `${formatNumber(terbanyak.jumlah)} KK` : '—';
        }
    }

    function renderDynamicLabels() {
        const totalRegion = state.detail.length;

        if (elements.cakupanData) {
            elements.cakupanData.innerHTML = totalRegion > 0
                ? `${totalRegion} kabupaten/kota menampilkan data KK per tahun.`
                : '&nbsp;';
        }
        if (elements.tabelSubtitle) {
            elements.tabelSubtitle.innerHTML = totalRegion > 0
                ? `Jumlah KK per kabupaten/kota pada tahun ${state.currentYear}.`
                : '&nbsp;';
        }
        if (elements.distribusiSubtitle) {
            elements.distribusiSubtitle.textContent = state.currentYear
                ? `Perbandingan jumlah KK antar wilayah pada tahun ${state.currentYear}.`
                : '&nbsp;';
        }
        if (elements.totalCount) elements.totalCount.textContent = totalRegion;
    }

    function renderDistribusi() {
        if (!elements.distribusiContainer) return;

        if (state.detail.length === 0) {
            elements.distribusiContainer.innerHTML = `<div class="text-center py-4 text-muted">Tidak ada data ditemukan</div>`;
            return;
        }

        const top = state.detail.slice(0, 5);
        const max = Math.max(...top.map(item => item.jumlah_kk), 1);

        elements.distribusiContainer.innerHTML = top.map(item => {
            const persen = Math.round((item.jumlah_kk / max) * 100);
            return `
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size: 13px; font-weight: 600; color: #1a1a2e;">${item.nama_kabupaten_kota}</span>
                        <span style="font-size: 13px; font-weight: 700; color: #1a1a2e;">${formatNumber(item.jumlah_kk)} KK</span>
                    </div>
                    <div class="progress" style="height: 10px; background-color: #e8f0ff; border-radius: 5px;">
                        <div class="progress-bar" style="width: ${persen}%; background: linear-gradient(90deg, #0d9488 0%, #14b8a6 100%); border-radius: 5px;"></div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderTable() {
        if (!elements.tableBody) return;

        if (elements.tableYearHead) {
            elements.tableYearHead.textContent = state.currentYear ? `KK ${state.currentYear}` : 'Jumlah KK';
        }

        const keyword = state.search.trim().toLowerCase();
        const filtered = keyword
            ? state.detail.filter(item => item.nama_kabupaten_kota.toLowerCase().includes(keyword))
            : state.detail;

        if (filtered.length === 0) {
            showTableLoading(keyword ? 'Tidak ada hasil ditemukan' : 'Tidak ada data ditemukan');
        } else {
            elements.tableBody.innerHTML = '';
            filtered.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-3">
                        <i class="bi bi-building-fill me-2" style="color: #0d9488;"></i>
                        <strong style="color: #1a1a2e;">${item.nama_kabupaten_kota}</strong>
                    </td>
                    <td class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e;">${formatNumber(item.jumlah_kk)}</td>
                `;
                elements.tableBody.appendChild(row);
            });
        }

        if (elements.showCount) elements.showCount.textContent = filtered.length;
        if (elements.totalCount) elements.totalCount.textContent = state.detail.length;
    }

    function renderTrendChart() {
        if (!elements.trenChart) return;
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js tidak tersedia');
            return;
        }
        if (state.trend.length === 0) return;

        const ctx = elements.trenChart.getContext('2d');
        if (window.trenKKChartInstance) window.trenKKChartInstance.destroy();

        const labels = state.trend.map(item => item.tahun);
        const data = state.trend.map(item => item.jumlah);

        window.trenKKChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah KK',
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
                                return 'Jumlah: ' + formatNumber(context.parsed.y) + ' KK';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { size: 11 },
                            color: '#8892a4',
                            callback: function(value) {
                                return formatNumber(value);
                            }
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

    // ==================== MAIN ====================

    async function loadInitialData() {
        const yearsLoaded = await fetchYears();
        if (!yearsLoaded || state.years.length === 0) {
            showTableError('Gagal memuat daftar tahun');
            return;
        }
        renderYearDropdown();
        await loadData(state.currentYear);
        await fetchTrend();
        renderTrendChart();
    }

    async function loadData(tahun) {
        state.currentYear = tahun;
        showTableLoading();

        const summaryOk = await fetchSummary(tahun);
        if (!summaryOk) {
            showTableError('Gagal memuat data dari server');
            return false;
        }
        renderSummary();

        const detailOk = await fetchDetail(tahun);
        if (!detailOk) {
            showTableError('Gagal memuat tabel');
            return false;
        }
        renderDynamicLabels();
        renderDistribusi();
        renderTable();

        return true;
    }

    function handleYearChange(event) {
        const selectedYear = parseInt(event.target.value, 10);
        if (selectedYear) {
            loadData(selectedYear);
        }
    }

    function handleSearchInput(event) {
        state.search = event.target.value;
        renderTable();
    }

    // ==================== EVENTS ====================

    function attachEventListeners() {
        if (elements.yearSelect) {
            elements.yearSelect.addEventListener('change', handleYearChange);
        }
        if (elements.searchInput) {
            elements.searchInput.addEventListener('input', handleSearchInput);
        }
    }

    // ==================== INIT ====================

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

})();