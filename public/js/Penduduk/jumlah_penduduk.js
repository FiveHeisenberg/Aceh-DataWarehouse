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
        DEFAULT_PER_PAGE: 25
    };

    // ==================== STATE ====================
    let state = {
        currentYear: null,
        years: [],
        strukturUmur: [],
        pyramid: null,
        kabupaten: [],
        details: [],
        summary: null,
        trendData: [],
        statusPerkawinan: []
    };

    // ==================== DOC ELEMENTS ====================
    const elements = {};

    function cacheElements() {
        elements.yearSelect = document.getElementById('filter-tahun');
        elements.statTotal = document.getElementById('stat-total');
        elements.statTotalSatuan = document.getElementById('stat-total-satuan');
        elements.statPertumbuhan = document.getElementById('stat-pertumbuhan');
        elements.tableBody = document.getElementById('table-body');
        elements.trendChart = document.getElementById('trendChart');
        elements.filterTrend = document.getElementById('filter-trend');

        // Untuk Diagram Piramida
        elements.pyramidChart = document.getElementById('pyramidChart');
        elements.legendTotalL = document.getElementById('legend-total-l');
        elements.legendTotalP = document.getElementById('legend-total-p');

        // Untuk Analisi Status Perkawinan
        elements.statSudahKawin = document.getElementById('stat-sudah-kawin');
        elements.statBelumKawin = document.getElementById('stat-belum-kawin');
        elements.statCeraiMati = document.getElementById('stat-cerai-mati');
        elements.statCeraiHidup = document.getElementById('stat-cerai-hidup');
    }

    // ==================== UTILITY FUNCTIONS ====================
    
    function formatNumber(num) {
        if (!num && num !== 0) return '—';
        return num.toLocaleString('id-ID');
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

    async function fetchIndexData(tahun) {
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

    async function fetchTableData(tahun) {
        try {
            let url = `${CONFIG.API_BASE_URL}/detail-penduduk`;
            const params = new URLSearchParams();
            if (tahun) params.set('tahun', tahun);
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

    async function fetchStrukturUmur(tahun) {
        try {
            let url = `${CONFIG.API_BASE_URL}/struktur-umur`;
            const params = new URLSearchParams();
            if (tahun) params.set('tahun', tahun);
            const qs = params.toString();
            if (qs) url += `?${qs}`;

            const response = await fetch(url);
            const result = await response.json();
            if (result.success) {
                state.strukturUmur = result.data;
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error fetch struktur umur:', error);
            return false;
        }
    }

    async function fetchPiramidaUmur(tahun) {
        try {
            let url = `${CONFIG.API_BASE_URL}/pyramid-umur`;
            const params = new URLSearchParams();
            if (tahun) params.set('tahun', tahun);
            const qs = params.toString();
            if (qs) url += `?${qs}`;

            const response = await fetch(url);
            const result = await response.json();
            if (result.success) {
                state.pyramid = result.data;
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error fetch piramida umur:', error);
            return false;
        }
    }

    async function fetchStatusPerkawinan(tahun) {
        try {
            let url = `${CONFIG.API_BASE_URL}/status-perkawinan`;
            const params = new URLSearchParams();
            if (tahun) params.set('tahun', tahun);
            const qs = params.toString();
            if (qs) url += `?${qs}`

            const response = await fetch(url);
            const result = await response.json();
            if (result.success) {
                state.statusPerkawinan = result.data;
                return true;
            }
            return false;
        } catch (error) {
            console.log('Error fetch Status Perkawinan:', error);
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
    }

    function renderStrukturUmur() {
        const container = document.getElementById('struktur-umur-container');
        if (!container) return;

        if (!state.strukturUmur || state.strukturUmur.length === 0) {
            container.innerHTML = `<div class="text-center py-4 text-muted">Tidak ada data ditemukan</div>`;
            return;
        }

        const colorMap = {
            '0-5': { bg: '#f4f6fb', dot: '#93c5fd', color: '#1a1a2e' },
            '6-9': { bg: '#f4f6fb', dot: '#5eead4', color: '#1a1a2e' },
            '10-17': { bg: '#f4f6fb', dot: '#38bdf8', color: '#1a1a2e' },
            '18-59': { bg: '#e8f5f0', dot: '#0d9488', color: '#0d9488' },
            '60+': { bg: '#f4f6fb', dot: '#c4c9d4', color: '#1a1a2e' },
            'Tidak Diketahui': { bg: '#f4f6fb', dot: '#8892a4', color: '#1a1a2e' },
        };

        container.innerHTML = state.strukturUmur
            .map((item) => {
                const c = colorMap[item.range_umur] || colorMap['Tidak Diketahui'];
                return `
                    <div class="d-flex align-items-center justify-content-between p-3 mb-2" style="background-color: ${c.bg}; border-radius: 10px;">
                        <div class="d-flex align-items-center">
                            <span class="rounded-circle me-3" style="width: 10px; height: 10px; background-color: ${c.dot}; display: inline-block; flex-shrink: 0;"></span>
                            <div>
                                <div style="font-weight: 700; color: ${c.color}; font-size: 14px;">${item.kategori} (${item.range_umur})</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div style="font-weight: 800; color: ${c.color}; font-size: 18px;">${formatNumber(item.jumlah)}</div>
                        </div>
                    </div>
                `;
            })
            .join('');
    }

    function niceMax(value) {
        if (value <= 0) {
            return 1;
        }
        const exp = Math.pow(10, Math.floor(Math.log10(value)));
        const f = value / exp;
        const nice = f <= 1 ? 1 : f <= 2 ? 2 : f <= 5 ? 5 : 10;
        return nice * exp;
    }

    // NAMPILIN DIAGRAM PIRAMIDA
    function renderPiramidaChart() {
        const d = state.pyramid;
        if (!d || !elements.pyramidChart || typeof Chart === 'undefined') return;

        const ctx = elements.pyramidChart.getContext('2d');
        if (window.pyramidChartInstance) window.pyramidChartInstance.destroy();

        const maxValue = Math.max(...d.laki_laki, ...d.perempuan, 1);
        const scale = niceMax(maxValue * 1.1);

        window.pyramidChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: d.labels,
                datasets: [
                    {
                        label: 'Laki-laki',
                        data: d.laki_laki.map(v => -v),
                        backgroundColor: '#2563a8',
                        borderRadius: 3,
                        barThickness: 14
                    },
                    {
                        label: 'Perempuan',
                        data: d.perempuan,
                        backgroundColor: '#0d9488',
                        borderRadius: 3,
                        barThickness: 14
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {display: false},
                    tooltip: {
                        callbacks: {
                            label: function (item) {
                                return item.dataset.label + ': ' + Math.abs(item.raw).toLocaleString('id-ID') + ' jiwa';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        min: -scale,
                        max: scale,
                        grid: {color: '#eef0f5'},
                        ticks: {
                            callback: function (value) {return Math.abs(value).toLocaleString('id-ID');},
                            color: '#8892a4',
                            font: {size: 11}
                        }
                    },
                    y: {
                        stacked: true,
                        grid: {display: false},
                        ticks: {color: '#5a6577', font: {size: 11}}
                    }
                }
            }
        });

        if (elements.legendTotalL) {
            elements.legendTotalL.textContent = 'Laki-laki (' + d.total_laki_laki.toLocaleString('id-ID') + ')';
        }
        if (elements.legendTotalP) {
            elements.legendTotalP.textContent = 'Perempuan (' + d.total_perempuan.toLocaleString('id-ID') + ')';
        }
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

    // NAMPILIN ANALISI STATUS PERKAWINAN
    function renderStatusPerkawinan() {
        const mapping = {
            'Sudah Kawin': elements.statSudahKawin,
            'Belum Kawin': elements.statBelumKawin,
            'Cerai Mati': elements.statCeraiMati,
            'Cerai Hidup': elements.statCeraiHidup,
        };

        if (!state.statusPerkawinan || state.statusPerkawinan.length === 0) {
            Object.values(mapping).forEach(el => { if (el) el.textContent = '-'; });
            return;
        }

        Object.values(mapping).forEach(el => { if (el) el.innerHTML = "Memuat . . ."; });

        state.statusPerkawinan.forEach((item) => {
            const el = mapping[item.status];
            if (el) el.textContent = formatNumber(item.jumlah);
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
        await fetchStrukturUmur(state.currentYear);
        renderStrukturUmur();
        await fetchPiramidaUmur(state.currentYear);
        renderPiramidaChart();
        await fetchStatusPerkawinan(state.currentYear);
        renderStatusPerkawinan();
    }

    async function loadData(tahun) {
        showLoading(elements.tableBody, 'Memuat data...');
        const success = await fetchIndexData(tahun);
        if (success) {
            renderSummary();
            renderTrendChart();
        } else {
            showError(elements.tableBody, 'Gagal memuat data dari server');
            return;
        }

        const tableOk = await fetchTableData(tahun);
        if (tableOk) {
            renderTable();
        } else {
            showError(elements.tableBody, 'Gagal memuat tabel');
        }

        await fetchStrukturUmur(tahun);
        renderStrukturUmur();
        await fetchPiramidaUmur(tahun);
        renderPiramidaChart();
        await fetchStatusPerkawinan(tahun);
        renderStatusPerkawinan();
    }

    function handleYearChange(event) {
        const selectedYear = parseInt(event.target.value);
        if (selectedYear) {
            state.currentYear = selectedYear;
            loadData(selectedYear);
        }
    }

    function handleTrendFilterChange(event) {
        const nama = event.target.value;

        if (!nama) {
            loadData(state.currentYear);
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

    // ==================== EVENT LISTENERS ====================

    function attachEventListeners() {
        if (elements.yearSelect) {
            elements.yearSelect.addEventListener('change', handleYearChange);
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