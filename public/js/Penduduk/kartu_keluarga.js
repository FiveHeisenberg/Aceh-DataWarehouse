/**
 * Aceh Data Warehouse - Kartu Keluarga Module
 * File: public/js/penduduk/kartu_keluarga.js
 * 
 * Mengelola chart dan visualisasi data Kartu Keluarga (Dummy Data)
 * TODO: Nanti tambahkan logika API integration
 */
(function() {
    'use strict';

    // ==================== KONFIGURASI ====================
    const CONFIG = {
        CHART_COLOR: '#0d9488',
        CHART_BG_COLOR: 'rgba(13, 148, 136, 0.1)'
    };

    // ==================== DUMMY DATA ====================
    // TODO: Nanti ganti dengan fetch dari API
    const dummyData = {
        labels: ['2020', '2021', '2022', '2023', '2024 (Saat ini)'],
        values: [1280000, 1310000, 1350000, 1380000, 1412850]
    };

    // ==================== CHART INSTANCE ====================
    let trenChartInstance = null;

    // ==================== UTILITY FUNCTIONS ====================
    
    /**
     * Format angka dengan separator ribuan
     */
    function formatNumber(num) {
        if (!num && num !== 0) return '—';
        return num.toLocaleString('id-ID');
    }

    // ==================== CHART FUNCTIONS ====================

    /**
     * Render Chart Tren Pertumbuhan KK
     * TODO: Nanti modifikasi untuk menerima data dari API
     */
    function renderTrenChart() {
        const canvas = document.getElementById('trenKKChart');
        if (!canvas || typeof Chart === 'undefined') {
            console.warn('Canvas chart tidak ditemukan atau Chart.js belum dimuat');
            return;
        }

        const ctx = canvas.getContext('2d');

        // Hancurkan chart lama jika ada (untuk prevent error re-render)
        if (trenChartInstance) {
            trenChartInstance.destroy();
        }

        trenChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dummyData.labels,
                datasets: [{
                    label: 'Jumlah KK',
                    data: dummyData.values,
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
                    legend: { 
                        display: false 
                    },
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
                        min: 1200000,
                        max: 1500000,
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
                                return context.tick.label === '2024 (Saat ini)' 
                                    ? CONFIG.CHART_COLOR 
                                    : '#5a6577';
                            }
                        },
                        grid: { display: false }
                    }
                }
            }
        });

        console.log('%c Kartu Keluarga - Chart rendered ', 'background: #0d9488; color: #fff; font-size: 11px; padding: 2px 6px; border-radius: 3px;');
    }

    // ==================== PUBLIC METHODS ====================
    // TODO: Nanti tambahkan method untuk update chart dari API

    /**
     * Update chart dengan data baru
     * @param {Array} labels - Array label tahun
     * @param {Array} values - Array nilai jumlah KK
     */
    function updateChartData(labels, values) {
        if (!trenChartInstance) {
            console.warn('Chart belum diinisialisasi. Panggil renderTrenChart() dulu.');
            return;
        }

        trenChartInstance.data.labels = labels;
        trenChartInstance.data.datasets[0].data = values;
        trenChartInstance.update();
        
        console.log('Chart updated with new data');
    }

    /**
     * Get current chart instance (untuk manipulasi lanjutan)
     */
    function getChartInstance() {
        return trenChartInstance;
    }

    // ==================== INITIALIZATION ====================

    function init() {
        console.log('%c Aceh Data Warehouse - Kartu Keluarga ', 'background: #0d9488; color: #fff; font-size: 12px; padding: 4px 8px; border-radius: 4px;');
        
        // Render chart saat halaman dimuat
        renderTrenChart();
    }

    // Jalankan saat DOM siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose public methods ke global scope (untuk debugging/development)
    window.KartuKeluargaChart = {
        updateChartData: updateChartData,
        getChartInstance: getChartInstance,
        renderTrenChart: renderTrenChart
    };

})();