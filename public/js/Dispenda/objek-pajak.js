/**
 * Aceh Data Warehouse - Modul Dispenda / Objek Pajak
 * File: public/js/Dispenda/objek-pajak.js
 *
 * Menangani dua grafik: tren pendaftaran (bar) & komposisi potensi (doughnut).
 * Canvas hanya ada di DOM kalau Blade memutuskan datanya cukup untuk sebuah tren,
 * jadi di sini cukup cek elemennya — tidak perlu branching empty state.
 */
(function () {
    'use strict';

    if (typeof Chart === 'undefined') return;

    const rupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    const COLORS = ['#0D9488', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'];

    function readJson(id) {
        const el = document.getElementById(id);
        if (!el) return null;
        try {
            return JSON.parse(el.textContent || '[]');
        } catch (e) {
            console.error('JSON tidak valid:', id, e);
            return null;
        }
    }

    // ------------------------------------------------- tren pendaftaran (bar)
    const trenEl = document.getElementById('chart-tren-pendaftaran');
    const tren = readJson('data-trend-pendaftaran');

    if (trenEl && tren && tren.length >= 2) {
        new Chart(trenEl, {
            type: 'bar',
            data: {
                labels: tren.map((r) => String(r.tahun)),
                datasets: [
                    {
                        label: 'Jumlah Objek',
                        // Cast eksplisit: string dari PDO akan menggagalkan sumbu Y.
                        data: tren.map((r) => Number(r.total) || 0),
                        backgroundColor: '#0D9488',
                        borderRadius: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (i) => Number(i.parsed.y).toLocaleString('id-ID') + ' objek' } },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#6b7280' } },
                    y: {
                        grid: { color: '#e5e7eb', borderDash: [3, 3] },
                        ticks: { color: '#6b7280', precision: 0 },
                    },
                },
            },
        });
    }

    // ------------------------------------------- komposisi potensi (doughnut)
    const komposisiEl = document.getElementById('chart-komposisi');
    const komposisi = readJson('data-komposisi');

    if (komposisiEl && komposisi && komposisi.length > 0) {
        new Chart(komposisiEl, {
            type: 'doughnut',
            data: {
                labels: komposisi.map((r) => r.nama_pajak),
                datasets: [
                    {
                        data: komposisi.map((r) => Number(r.total) || 0),
                        backgroundColor: komposisi.map((_, i) => COLORS[i % COLORS.length]),
                        borderWidth: 2,
                        borderColor: '#ffffff',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '52%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: (item) => {
                                const total = item.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? Math.round((item.parsed / total) * 100) : 0;
                                return ' ' + rupiah(item.parsed) + ' (' + pct + '%)';
                            },
                        },
                    },
                },
            },
        });
    }
})();
