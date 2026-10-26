@extends('layouts.app')

@section('content')

<style>
    .dw-card { border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; }
    .sec-title { font-weight: 700; font-size: 1.5rem; }
    .dist-row { margin-bottom: .9rem; }
    .dist-row .head { display: flex; justify-content: space-between; font-size: .8rem; font-weight: 600; margin-bottom: .2rem; }
    .dist-row .progress { height: 9px; background: #e5eef6; }
    .dist-row .progress-bar { background: #0f9d8a; }
    .page-loading {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .78);
        backdrop-filter: blur(2px);
    }

    /* Area scroll chart Perbandingan Harga */
    .chart-banding-scroll {
        max-height: 330px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: .4rem;
        scrollbar-width: thin;
        scrollbar-color: #c3c8d0 transparent;
    }

    .chart-banding-scroll::-webkit-scrollbar {
        width: 8px;
    }

    .chart-banding-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .chart-banding-scroll::-webkit-scrollbar-thumb {
        background: #c3c8d0;
        border-radius: 8px;
    }
</style>

<div class="container-fluid py-3">

    {{-- Breadcrumb + filter tahun --}}
    <div class="d-flex justify-content-between align-items-start mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Ekonomi Pangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pangan</li>
            </ol>
        </nav>
        <select id="filterTahun" class="form-select form-select-sm w-auto">
            @foreach ($tahunList as $t)
                <option value="{{ $t }}" @selected((int) $t === (int) $tahunAktif)>{{ $t }}</option>
            @endforeach
        </select>
    </div>

    <h2 class="fw-bold mb-3">Harga Pangan</h2>

    {{-- Tren harga & perbandingan harga --}}
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="fw-bold mb-0">Tren Harga</h4>
                    <select id="selKabHarga" class="form-select form-select-sm w-auto">
                        @foreach ($kabHargaList as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-muted small">Harga rata-rata per bulan sesuai satuan komoditas</div>
                <div id="chartTren"></div>
                <select id="selKomTren" class="form-select mt-2">
                    @foreach ($komoditasHarga as $k)
                        <option value="{{ $k }}">{{ $k }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="fw-bold mb-0">Perbandingan Harga</h4>
                    <select id="selKomBanding" class="form-select form-select-sm w-auto">
                        @foreach ($komoditasHarga as $k)
                            <option value="{{ $k }}" {{ $k === 'Cabai Rawit' ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Daftar bar per kab/kota, bisa di-scroll ke bawah --}}
                <div class="chart-banding-scroll" id="scrollBanding">
                    <div id="chartBanding"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ketersediaan dan stok --}}
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <div class="sec-title">Ketersediaan dan Stok</div>
        <select id="selKomStok" class="form-select w-auto">
            @foreach ($komoditasStok as $k)
                <option value="{{ $k }}" {{ $k === 'Beras' ? 'selected' : '' }}>{{ $k }}</option>
            @endforeach
        </select>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="dw-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0">Trend Pertumbuhan Ketersediaan Stok</h6>
                    <select id="selKabStok" class="form-select form-select-sm w-auto">
                        @foreach ($wilayahStokList as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-muted small">Stok per bulan ({{ $tahunAktif }}), ton</div>
                <div id="chartStok"></div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="dw-card p-3 h-100">
                <h6 class="fw-bold mb-0">Chart Distribusi Wilayah</h6>
                <div class="text-muted small mb-3" id="subDistribusi">Perbandingan jumlah ketersediaan stok</div>
                <div id="distribusiWrap"></div>
            </div>
        </div>
    </div>
</div>

<div id="pageLoading" class="page-loading d-none" role="status" aria-live="polite">
    <div class="text-center">
        <div class="spinner-border text-success" role="status" aria-hidden="true"></div>
        <div id="loadingText" class="mt-2 small fw-semibold text-dark">
            Memuat data...
        </div>
    </div>
</div>

{{-- ApexCharts (hapus jika layouts.app sudah memuatnya) --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bulanHarga   = @json($bulanHarga);
        const trenHarga    = @json($trenHarga);
        const perbandingan = @json($perbandingan);
        const bulanStok    = @json($bulanStok);
        const stok         = @json($stok);
        const tahun        = {{ $tahunAktif }};

        const fmt0 = n => Number(n).toLocaleString('id-ID');
        const fmt2 = n => Number(n).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const rp   = n => 'Rp ' + fmt0(n);
        const $    = id => document.getElementById(id);
        const satuanHarga = @json($satuanHarga);
        const filterTahun = $('filterTahun');
        const pageLoading = $('pageLoading');
        const loadingText = $('loadingText');
        const noDataHarga = `Data harga belum tersedia untuk tahun ${tahun}`;

        filterTahun.addEventListener('change', function () {
            const selectedYear = this.value;

            filterTahun.disabled = true;
            filterTahun.setAttribute('aria-busy', 'true');
            loadingText.textContent = `Memuat data tahun ${selectedYear}...`;
            pageLoading.classList.remove('d-none');
            document.body.style.cursor = 'wait';

            const url = new URL(window.location.href);
            url.searchParams.set('tahun', selectedYear);

            setTimeout(() => {
                window.location.href = url.toString();
            }, 50);
        });

        window.addEventListener('pageshow', function () {
            pageLoading.classList.add('d-none');
            loadingText.textContent = 'Memuat data...';
            filterTahun.disabled = false;
            filterTahun.removeAttribute('aria-busy');
            document.body.style.cursor = '';
        });

        // ---------- 1. Tren harga ----------
        function optTren() {
            const komoditas = $('selKomTren').value;
            const data = (trenHarga[komoditas]?.[$('selKabHarga').value]) || [];
            const satuan = satuanHarga[komoditas] || 'kg';

            return {
                series: [{ name: 'Harga rata-rata', data: data }],
                noData: { text: noDataHarga, style: { fontSize: '12px', color: '#64748b' } },
                xaxis: { categories: bulanHarga.map(b => b.substring(0, 3)), title: { text: 'Bulan ' + tahun, style: { fontSize: '11px' } } },
                yaxis: { title: { text: 'Harga (Rp/' + satuan + ')', style: { fontSize: '11px' } }, labels: { formatter: v => fmt0(Math.round(v)) } },
                tooltip: { y: { formatter: v => rp(v) + '/' + satuan } }
            };
        }
        const chartTren = new ApexCharts($('chartTren'), Object.assign({
            chart: { type: 'line', height: 230, toolbar: { show: false } },
            stroke: { curve: 'straight', width: 2.5 },
            markers: { size: 4 },
            colors: ['#4f94d6'],
            dataLabels: { enabled: false },
            grid: { strokeDashArray: 3 }
        }, optTren()));
        chartTren.render();
        ['selKomTren', 'selKabHarga'].forEach(id => $(id).addEventListener('change', () => chartTren.updateOptions(optTren())));

        // ---------- 2. Perbandingan harga (bisa di-scroll) ----------
        function optBanding() {
            const komoditas = $('selKomBanding').value;
            const data = perbandingan[komoditas] || {};
            const satuan = satuanHarga[komoditas] || 'kg';
            const rows = Object.entries(data).sort((a, b) => b[1] - a[1]);

            return {
                // tinggi chart mengikuti jumlah baris (33px/baris) agar bisa di-scroll di dalam kartu
                chart: { type: 'bar', height: Math.max(260, rows.length * 33 + 40), toolbar: { show: false } },
                series: [{ name: 'Harga', data: rows.map(r => r[1]) }],
                noData: { text: noDataHarga, style: { fontSize: '12px', color: '#64748b' } },
                xaxis: { categories: rows.map(r => r[0].replace('Kabupaten ', 'Kab. ')), labels: { formatter: v => fmt0(Math.round(v)) } },
                yaxis: { labels: { style: { fontSize: '11px', colors: '#475569' }, maxWidth: 150 } },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '70%',
                        borderRadius: 4,
                        dataLabels: { position: 'center' }   // nilai di tengah batang
                    }
                },
                colors: ['#b0386b'],
                dataLabels: {
                    enabled: true,
                    formatter: v => fmt0(v),
                    textAnchor: 'middle',
                    style: { fontSize: '11px', fontWeight: 600, colors: ['#0f172a'] },
                    background: { enabled: false }
                },
                tooltip: { y: { formatter: v => rp(v) + '/' + satuan } },
                grid: { strokeDashArray: 3 }
            };
        }
        const chartBanding = new ApexCharts($('chartBanding'), optBanding());
        chartBanding.render();
        $('selKomBanding').addEventListener('change', () => {
            chartBanding.updateOptions(optBanding());
            $('scrollBanding').scrollTop = 0;   // kembali ke atas saat komoditas diganti
        });

        // ---------- 3. Tren ketersediaan stok ----------
        function optStok() {
            const data = (stok[$('selKomStok').value]?.[$('selKabStok').value]) || [];
            return {
                series: [{ name: 'Stok (ton)', data: data }],
                noData: { text: `Data stok belum tersedia untuk tahun ${tahun}`, style: { fontSize: '12px', color: '#64748b' } },
                xaxis: { categories: bulanStok.map(b => b.substring(0, 3)) },
                yaxis: { labels: { formatter: v => fmt0(Math.round(v)) }, title: { text: 'Stok (ton)', style: { fontSize: '11px' } } },
                tooltip: { y: { formatter: v => fmt2(v) + ' ton' } }
            };
        }
        const chartStok = new ApexCharts($('chartStok'), Object.assign({
            chart: { type: 'area', height: 340, toolbar: { show: false } },
            stroke: { curve: 'smooth', width: 2.5 },
            fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
            markers: { size: 3 },
            colors: ['#0f9d8a'],
            dataLabels: { enabled: false },
            grid: { strokeDashArray: 3 }
        }, optStok()));
        chartStok.render();

        // ---------- 4. Distribusi wilayah (bulan terakhir, top 5) ----------
        function renderDistribusi() {
            const kom = $('selKomStok').value;
            const rows = Object.entries(stok[kom] || {})
                .map(([wil, seri]) => [wil, seri[seri.length - 1] || 0])
                .filter(([, value]) => value > 0)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 5);

            if (!rows.length) {
                $('subDistribusi').textContent = 'Data ketersediaan stok belum tersedia';
                $('distribusiWrap').innerHTML = '<div class="text-muted small">Tidak ada data untuk ditampilkan.</div>';
                return;
            }

            const max = rows[0][1] || 1;
            const bulanTerakhir = bulanStok[bulanStok.length - 1] || '-';
            $('subDistribusi').textContent = 'Perbandingan jumlah ketersediaan stok ' + kom + ' (' + bulanTerakhir + ' ' + tahun + ')';
            $('distribusiWrap').innerHTML = rows.map(r => `
                <div class="dist-row">
                    <div class="head"><span>${r[0]}</span><span>${fmt2(r[1])} ton</span></div>
                    <div class="progress"><div class="progress-bar" style="width:${(r[1] / max * 100).toFixed(1)}%"></div></div>
                </div>`).join('');
        }
        renderDistribusi();

        $('selKomStok').addEventListener('change', () => { chartStok.updateOptions(optStok()); renderDistribusi(); });
        $('selKabStok').addEventListener('change', () => chartStok.updateOptions(optStok()));
    });
</script>
@endsection