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
        search: '',
        kkTotal: null,
        anggota: null,
        mapMode: 'rata'
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
        elements.distribusiWrapper = document.getElementById('distribusi-chart-wrapper')
        elements.distribusiList = document.getElementById('distribusi-list');
        elements.distribusiKosong = document.getElementById('distribusi-kosong')
        elements.searchInput = document.getElementById('kk-search');
        elements.tableBody = document.getElementById('kk-table-body');
        elements.tableYearHead = document.getElementById('kk-table-year-head');
        elements.showCount = document.getElementById('kk-show-count');
        elements.totalCount = document.getElementById('kk-total-count');
        elements.cakupanData = document.getElementById('cakupan-data');
        elements.tabelSubtitle = document.getElementById('tabel-subtitle');
        elements.statRataAnggota = document.getElementById('stat-rata-anggota');
        elements.statRataAnggotaSub = document.getElementById('stat-rata-anggota-sub');
        elements.statAnggotaTertinggi = document.getElementById('stat-anggota-tertinggi');
        elements.statAnggotaTerendah = document.getElementById('stat-anggota-terendah');
        elements.distribusiAnggotaChart = document.getElementById('distribusiAnggotaChart');
        elements.petaAnggota = document.getElementById('peta-anggota');
        elements.petaAnggotaLegend = document.getElementById('peta-anggota-legend');
        elements.mapModeSelect = document.getElementById('map-mode');
    }

    // ==================== UTILITY ====================

    function formatNumber(num) {
        if (!num && num !== 0) return '—';
        return num.toLocaleString('id-ID');
    }

    function formatDecimal(num, digits = 2) {
        if (num === null || num === undefined || Number.isNaN(Number(num))) return '—';
        return Number(num).toLocaleString('id-ID', {
            minimumFractionDigits: digits,
            maximumFractionDigits: digits
        });
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

    // JUMLAH KK
    async function fetchTotalKartuKeluarga(tahun) {
        try {
            const url = `${CONFIG.API_BASE_URL}/jumlah-kk?tahun=${tahun}`;
            const response = await fetch(url);
            const result = await response.json();
            if (result.success) {
                state.kkTotal = result.data;
                return true;
            }
            return false;
        } catch (error) {
            console.log('Error fetch total KK: ', error);
            return false;
        }
    }

    // ANALISIS ANGGOTA KELUARGA
    async function fetchAnggota(tahun) {
        try {
            const url = `${CONFIG.API_BASE_URL}/kartu-keluarga/anggota?tahun=${tahun}`;
            const response = await fetch(url);
            const result = await response.json();
            if (result.success) {
                state.anggota = result.data;
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error fetch anggota keluarga:', error);
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

        elements.yearSelect.value = state.years[0];
        state.currentYear = Number(elements.yearSelect.value);
    }

    function renderSummary() {
        if (!state.summary) return;

        if (elements.statYearBadge) elements.statYearBadge.textContent = `${state.summary.tahun}`;
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
            elements.statTerbanyakJumlah.textContent = terbanyak ? `${formatNumber(terbanyak.jumlah)}` : '—';
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
            if (totalRegion > 0 && state.currentYear) {
                elements.distribusiSubtitle.textContent = `${totalRegion} kabupaten/kota - Total ${formatNumber(sumDetailKK())} KK pada tahun ${state.currentYear}.`;
            } else {
                elements.distribusiSubtitle.textContent = '&nbsp;';
            }
        }
        if (elements.totalCount) elements.totalCount.textContent = totalRegion;
    }

    function sumDetailKK() {
        return state.detail.reduce((total, item) => total + (
            Number(item.jumlah_kk) ||
        0), 0)
    }

    function setDistribusiKosong(message) {
        if (elements.distribusiWrapper) elements.distribusiWrapper.classList.add('d-none');
        if (elements.distribusiKosong) {
            elements.distribusiKosong.textContent = message;
            elements.distribusiKosong.classList.remove('d-none');
        }
    }

    function escapeHtml(text) {
        return String(text).replace(/[&<>"']/g, ch => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[ch]));
    }

    function renderDistribusi() {
        if (!elements.distribusiList || !elements.distribusiWrapper) return;

        // Bersihkan instance Chart.js lama (jika masih ada)
        if (window.distribusiKKChartInstance) {
            window.distribusiKKChartInstance.destroy();
            window.distribusiKKChartInstance = null;
        }

        if (state.detail.length === 0) {
            elements.distribusiList.innerHTML = '';
            setDistribusiKosong('Tidak ada data ditemukan');
            return;
        }

        elements.distribusiWrapper.classList.remove('d-none');
        if (elements.distribusiKosong) elements.distribusiKosong.classList.add('d-none');

        const totalKK = sumDetailKK();
        const items = state.detail.map(item => ({
            nama: item.nama_kabupaten_kota,
            nilai: Number(item.jumlah_kk) || 0
        }));
        const maxValue = Math.max(...items.map(i => i.nilai), 0);

        elements.distribusiList.innerHTML = items.map((item, index) => {
            const lebar = maxValue > 0 ? (item.nilai / maxValue) * 100 : 0;
            const persen = totalKK > 0 ? (item.nilai / totalKK) * 100 : 0;
            const tooltip = `${formatNumber(item.nilai)} KK - ${persen.toLocaleString('id-ID', {maximumFractionDigits: 1})}% dari total`;
            const margin = index === items.length - 1 ? '0' : '22px';

            return `
                <div style="margin-bottom: ${margin};" title="${tooltip}">
                    <div class="d-flex align-items-center justify-content-between" style="margin-bottom: 8px; gap: 12px;">
                        <span style="font-size: 16px; font-weight: 500; color: #1a1a2e;">${escapeHtml(item.nama)}</span>
                        <span style="font-size: 16px; font-weight: 800; color: #1a1a2e; white-space: nowrap;">${formatNumber(item.nilai)} KK</span>
                    </div>
                    <div style="width: 100%; height: 14px; background-color: #e8effe; border-radius: 999px; overflow: hidden;">
                        <div class="distribusi-bar-fill" data-width="${lebar}" style="width: 0; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #0d9488 0%, #14b8a6 100%); transition: width 0.6s ease;"></div>
                    </div>
                </div>`;
        }).join('');

        // Animasi bar: mulai dari 0 lalu tumbuh ke lebar sebenarnya
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                elements.distribusiList.querySelectorAll('.distribusi-bar-fill').forEach(bar => {
                    bar.style.width = bar.dataset.width + '%';
                });
            });
        });
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

    // NAMPILIN JUMLAH TOTAL KK
    function renderTotalKK() {
        if (!state.kkTotal) return;
        if (elements.statTotal) elements.statTotal.textContent = formatNumber(state.kkTotal.total_kk);
        if (elements.statYearBadge) elements.statYearBadge.textContent = `${state.kkTotal.tahun}`;
    }

    // ==================== ANALISIS ANGGOTA KELUARGA ====================

    const PURPLE_SCALE = ['#ede9fe', '#c4b5fd', '#a78bfa', '#7c3aed', '#4c1d95'];
    const KATEGORI_COLORS = {
        kecil: '#60a5fa',
        sedang: '#fbbf24',
        besar: '#f87171',
        sangat_besar: '#a855f7'
    };
    const KATEGORI_LABELS = {
        kecil: 'Kecil (1-2)',
        sedang: 'Sedang (3-4)',
        besar: 'Besar (5-6)',
        sangat_besar: 'Sangat Besar (7+)'
    };
    const KATEGORI_NAMA = {
        kecil: 'Kecil',
        sedang: 'Sedang',
        besar: 'Besar',
        sangat_besar: 'Sangat Besar'
    };

    let petaAnggotaMap = null;
    let petaAnggotaLayer = null;
    const petaAnggotaLayers = new Map();
    let petaAnggotaData = new Map();
    let petaAnggotaInitPromise = null;

    function normalizeNama(nama) {
        return String(nama || '')
            .replace(/^(Kabupaten|Kota)\s+/i, '')
            .trim()
            .toLowerCase();
    }

    function rataRange() {
        const values = [...petaAnggotaData.values()]
            .map(row => Number(row.rata_rata))
            .filter(value => Number.isFinite(value));

        if (values.length === 0) return { min: 0, max: 0 };
        return { min: Math.min(...values), max: Math.max(...values) };
    }

    function warnaRata(nilai) {
        const value = Number(nilai);
        if (!Number.isFinite(value)) return '#e5e7eb';

        const { min, max } = rataRange();
        if (max <= min) return PURPLE_SCALE[PURPLE_SCALE.length - 1];

        const ratio = (value - min) / (max - min);
        const index = Math.min(
            PURPLE_SCALE.length - 1,
            Math.floor(ratio * PURPLE_SCALE.length)
        );
        return PURPLE_SCALE[index];
    }

    function warnaWilayah(row) {
        if (!row) return '#e5e7eb';
        if (state.mapMode === 'kategori') {
            return KATEGORI_COLORS[row.kategori_dominan] || '#e5e7eb';
        }
        return warnaRata(row.rata_rata);
    }

    function stylePetaAnggota(feature) {
        const key = normalizeNama(feature.properties?.nama);
        const row = petaAnggotaData.get(key);

        return {
            color: '#ffffff',
            weight: 1.5,
            fillColor: warnaWilayah(row),
            fillOpacity: row ? 0.9 : 0.3
        };
    }

    function tooltipPetaAnggota(props) {
        const key = normalizeNama(props?.nama);
        const row = petaAnggotaData.get(key);

        if (!row) {
            return `<strong>${props?.nama || '-'}</strong><br>Tidak ada data anggota keluarga`;
        }

        if (state.mapMode === 'kategori') {
            const p = row.proporsi || {};
            const j = row.jumlah_kategori || {};
            const dominan = KATEGORI_NAMA[row.kategori_dominan] || row.kategori_dominan_label;

            return `<strong>${row.nama}</strong><br>
                Dominan: ${dominan}<br>
                Kecil (1-2): ${formatNumber(j.kecil)} KK (${formatDecimal(p.kecil, 1)}%)<br>
                Sedang (3-4): ${formatNumber(j.sedang)} KK (${formatDecimal(p.sedang, 1)}%)<br>
                Besar (5-6): ${formatNumber(j.besar)} KK (${formatDecimal(p.besar, 1)}%)<br>
                Sangat Besar (7+): ${formatNumber(j.sangat_besar)} KK (${formatDecimal(p.sangat_besar, 1)}%)`;
        }

        return `<strong>${row.nama}</strong><br>
            Rata-rata: ${formatDecimal(row.rata_rata, 2)} anggota/KK<br>
            ${formatNumber(row.total_penduduk)} jiwa / ${formatNumber(row.total_kk)} KK`;
    }

    function renderLegendAnggota() {
        const el = elements.petaAnggotaLegend;
        if (!el) return;

        if (state.mapMode === 'kategori') {
            const items = Object.keys(KATEGORI_LABELS).map(key => `
                <div class="legend-item">
                    <span class="legend-swatch" style="background:${KATEGORI_COLORS[key]}"></span>
                    ${KATEGORI_LABELS[key]}
                </div>`).join('');

            el.innerHTML = `<div class="legend-title">Kategori Ukuran Keluarga (dominan)</div>${items}`;
            return;
        }

        const { min, max } = rataRange();
        const step = (max - min) / PURPLE_SCALE.length;

        const items = PURPLE_SCALE.map((color, index) => {
            const low = min + step * index;
            const high = min + step * (index + 1);
            const label = index === PURPLE_SCALE.length - 1
                ? `≥ ${formatDecimal(low, 2)}`
                : `${formatDecimal(low, 2)} – ${formatDecimal(high, 2)}`;

            return `<div class="legend-item">
                <span class="legend-swatch" style="background:${color}"></span>${label}
            </div>`;
        }).join('');

        el.innerHTML = `<div class="legend-title">Rata-rata Anggota per KK</div>${items}`;
    }

    function applyPetaAnggotaStyle() {
        if (!petaAnggotaLayer) return;

        petaAnggotaLayer.setStyle(stylePetaAnggota);
        petaAnggotaLayers.forEach(layer => {
            layer.setTooltipContent(tooltipPetaAnggota(layer.feature?.properties));
        });
        renderLegendAnggota();
    }

    async function initPetaAnggota() {
        if (petaAnggotaInitPromise) return petaAnggotaInitPromise;
        if (typeof L === 'undefined' || !elements.petaAnggota) return null;

        petaAnggotaInitPromise = (async () => {
            petaAnggotaMap = L.map(elements.petaAnggota, { scrollWheelZoom: false })
                .setView([4.7, 96.8], 8);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 18
            }).addTo(petaAnggotaMap);

            try {
                const geojsonUrl = elements.petaAnggota.dataset.geojson
                    || '/js/Penduduk/aceh-kabupaten.geojson';
                const geojson = await fetch(geojsonUrl)
                    .then(response => response.json());

                petaAnggotaLayer = L.geoJSON(geojson, {
                    style: stylePetaAnggota,
                    onEachFeature: (feature, layer) => {
                        petaAnggotaLayers.set(
                            normalizeNama(feature.properties?.nama),
                            layer
                        );

                        layer.bindTooltip(
                            () => tooltipPetaAnggota(feature.properties),
                            { sticky: true }
                        );

                        layer.on({
                            mouseover: event => {
                                event.target.setStyle({
                                    weight: 3,
                                    color: '#0f172a',
                                    fillOpacity: 0.95
                                });
                                event.target.bringToFront();
                            },
                            mouseout: event => {
                                if (petaAnggotaLayer) {
                                    petaAnggotaLayer.resetStyle(event.target);
                                }
                            }
                        });
                    }
                }).addTo(petaAnggotaMap);

                petaAnggotaMap.fitBounds(petaAnggotaLayer.getBounds(), { padding: [12, 12] });
                applyPetaAnggotaStyle();
            } catch (error) {
                console.error('Gagal memuat peta anggota keluarga:', error);
                if (elements.petaAnggota) {
                    elements.petaAnggota.innerHTML = '<div class="text-danger small p-2">Peta gagal dimuat.</div>';
                }
            }

            return true;
        })();

        return petaAnggotaInitPromise;
    }

    function renderPetaAnggota() {
        const wilayah = state.anggota?.wilayah || [];
        petaAnggotaData = new Map(
            wilayah.map(row => [normalizeNama(row.nama), row])
        );

        if (!petaAnggotaMap) {
            initPetaAnggota();
            return;
        }

        applyPetaAnggotaStyle();
    }

    function renderDistribusiAnggotaChart() {
        const canvas = elements.distribusiAnggotaChart;
        if (!canvas || typeof Chart === 'undefined') return;

        const distribusi = state.anggota?.distribusi || [];
        if (window.distribusiAnggotaChartInstance) {
            window.distribusiAnggotaChartInstance.destroy();
        }

        const ctx = canvas.getContext('2d');
        window.distribusiAnggotaChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: distribusi.map(item => item.label),
                datasets: [{
                    label: 'Jumlah KK',
                    data: distribusi.map(item => item.jumlah_kk),
                    backgroundColor: ['#a78bfa', '#8b5cf6', '#7c3aed', '#4c1d95'],
                    borderRadius: 6,
                    maxBarThickness: 64
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
                            label: context => {
                                const item = distribusi[context.dataIndex] || {};
                                return `${formatNumber(context.parsed.y)} KK (${formatDecimal(item.persentase, 1)}%)`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#8892a4',
                            callback: value => formatNumber(value)
                        },
                        grid: { color: '#e0e4f0', drawBorder: false }
                    },
                    x: {
                        ticks: { color: '#5a6577', font: { size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    function renderAnggota() {
        if (!state.anggota) return;
        const ringkasan = state.anggota.ringkasan || {};

        if (elements.statRataAnggota) {
            elements.statRataAnggota.textContent = formatDecimal(ringkasan.rata_rata_anggota, 2);
        }
        if (elements.statRataAnggotaSub) {
            elements.statRataAnggotaSub.textContent =
                `${formatNumber(ringkasan.total_penduduk)} penduduk / ${formatNumber(ringkasan.total_kk)} KK`;
        }
        if (elements.statAnggotaTertinggi) {
            elements.statAnggotaTertinggi.textContent = ringkasan.tertinggi
                ? `${ringkasan.tertinggi.nama} (${formatDecimal(ringkasan.tertinggi.rata_rata, 2)})`
                : '—';
        }
        if (elements.statAnggotaTerendah) {
            elements.statAnggotaTerendah.textContent = ringkasan.terendah
                ? `${ringkasan.terendah.nama} (${formatDecimal(ringkasan.terendah.rata_rata, 2)})`
                : '—';
        }

        renderDistribusiAnggotaChart();
        renderPetaAnggota();
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
            setDistribusiKosong('Gagal memuat data dari server');
            return false;
        }
        renderSummary();

        const detailOk = await fetchDetail(tahun);
        if (!detailOk) {
            showTableError('Gagal memuat tabel');
            setDistribusiKosong('Gagal memuat data dari server');
            return false;
        }
        renderDynamicLabels();
        renderDistribusi();
        renderTable();

        const anggotaOk = await fetchAnggota(tahun);
        if (anggotaOk) renderAnggota();

        const totalOk = await fetchTotalKartuKeluarga(tahun);
        if(totalOk) renderTotalKK();
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

    function handleMapModeChange(event) {
        state.mapMode = event.target.value;
        applyPetaAnggotaStyle();
    }

    // ==================== EVENTS ====================

    function attachEventListeners() {
        if (elements.yearSelect) {
            elements.yearSelect.addEventListener('change', handleYearChange);
        }
        if (elements.searchInput) {
            elements.searchInput.addEventListener('input', handleSearchInput);
        }
        if (elements.mapModeSelect) {
            elements.mapModeSelect.addEventListener('change', handleMapModeChange);
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