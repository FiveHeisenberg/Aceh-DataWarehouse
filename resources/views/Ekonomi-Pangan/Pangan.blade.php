@extends('layouts.app')

@section('content')
@php
    // =====================================================================
    // DATA STATIS
    // Catatan: angka yang bertanda [DATA ASLI] berasal dari contoh yang kamu
    // berikan. Selebihnya adalah data contoh agar semua dropdown berfungsi.
    // =====================================================================
    $tahunList  = [2026, 2025, 2024];
    $tahunAktif = 2026;

    $komoditasHarga = ['Cabai Merah', 'Cabai Rawit', 'Bawang Merah', 'Bawang Putih', 'Beras', 'Gula Pasir', 'Minyak Goreng', 'Daging Ayam', 'Daging Sapi', 'Telur Ayam'];
    $komoditasStok  = ['Jagung', 'Cabai Merah', 'Cabai Rawit', 'Bawang Merah', 'Beras'];

    // ---------- 1. TREN HARGA (rata-rata per bulan, Rp/kg) ----------
    $bulanHarga = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober'];
    $kabHargaList = ['Kota Langsa', 'Kota Banda Aceh', 'Kota Lhokseumawe', 'Kabupaten Aceh Utara', 'Kabupaten Aceh Besar'];

    $hargaDasar = [   // harga dasar contoh (Rp/kg)
        'Cabai Merah' => 56000, 'Cabai Rawit' => 77000, 'Bawang Merah' => 38000, 'Bawang Putih' => 36000,
        'Beras' => 14500, 'Gula Pasir' => 17500, 'Minyak Goreng' => 18000, 'Daging Ayam' => 38000,
        'Daging Sapi' => 140000, 'Telur Ayam' => 29000,
    ];
    $faktorBulan = [1.00, 0.95, 1.04, 1.02, 1.01, 1.03, 0.97, 0.93, 0.98, 1.06];
    $faktorKab   = ['Kota Langsa' => 1.00, 'Kota Banda Aceh' => 1.04, 'Kota Lhokseumawe' => 0.99, 'Kabupaten Aceh Utara' => 0.97, 'Kabupaten Aceh Besar' => 1.01];

    $trenHarga = [];
    foreach ($komoditasHarga as $kom) {
        foreach ($kabHargaList as $kab) {
            $trenHarga[$kom][$kab] = array_map(
                fn($f) => (int) (round($hargaDasar[$kom] * $f * $faktorKab[$kab] / 25) * 25),
                $faktorBulan
            );
        }
    }
    // [DATA ASLI] Kota Langsa - Cabai Merah
    $trenHarga['Cabai Merah']['Kota Langsa'] = [59100, 50050, 59880, 57425, 57850, 59520, 52925, 45220, 52025, 64300];

    // ---------- 2. PERBANDINGAN HARGA (per kab/kota, 12 Oktober 2026) ----------
    $tanggalHarga = '12 Oktober 2026';
    $hargaRawit = [   // Rp/kg, Cabai Rawit
        'Kabupaten Simeulue'          => 87600,   // [DATA ASLI]
        'Kota Subulussalam'           => 82100,   // [DATA ASLI]
        'Kota Sabang'                 => 81300,   // [DATA ASLI]
        'Kabupaten Aceh Singkil'      => 80800,   // [DATA ASLI]
        'Kabupaten Gayo Lues'         => 80300,   // [DATA ASLI]
        'Kabupaten Aceh Tenggara'     => 79400,   // [DATA ASLI]
        'Kabupaten Aceh Selatan'      => 78500,
        'Kabupaten Aceh Barat Daya'   => 77800,
        'Kabupaten Nagan Raya'        => 77200,
        'Kabupaten Aceh Jaya'         => 76600,
        'Kabupaten Aceh Barat'        => 76000,
        'Kabupaten Bener Meriah'      => 75400,
        'Kabupaten Aceh Tamiang'      => 74900,
        'Kota Langsa'                 => 74300,
        'Kabupaten Aceh Timur'        => 73800,
        'Kabupaten Pidie Jaya'        => 73300,
        'Kabupaten Bireuen'           => 72800,
        'Kabupaten Pidie'             => 72300,
        'Kota Lhokseumawe'            => 71800,
        'Kabupaten Aceh Utara'        => 71300,
        'Kabupaten Aceh Besar'        => 70800,
        'Kota Banda Aceh'             => 70400,
        'Kabupaten Aceh Tengah'       => 70000,   // [DATA ASLI] terendah
    ];

    $perbandingan = [];
    foreach ($komoditasHarga as $kom) {
        foreach ($hargaRawit as $wil => $harga) {
            $perbandingan[$kom][$wil] = $kom === 'Cabai Rawit'
                ? $harga
                : (int) (round($hargaDasar[$kom] * ($harga / 77000) / 50) * 50);
        }
    }

    // ---------- 3 & 4. KETERSEDIAAN STOK (ton) ----------
    $bulanStok = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September'];
    $stokBerasAcehUtara = [8762.57, 8705.58, 10017.56, 11562.68, 12086.17, 11301.55, 11114.87, 12279.28, 13804.31]; // [DATA ASLI]

    // Stok September 2026 komoditas Beras per wilayah [DATA ASLI]
    $stokSeptBeras = [
        'Kabupaten Aceh Utara' => 13804.31,
        'Kabupaten Pidie'      => 9953.41,
        'Kabupaten Aceh Timur' => 9895.96,
        'Kabupaten Bireuen'    => 9089.00,
        'Kabupaten Aceh Besar' => 7519.76,
    ];
    $faktorStok = ['Jagung' => 0.42, 'Cabai Merah' => 0.06, 'Cabai Rawit' => 0.035, 'Bawang Merah' => 0.05, 'Beras' => 1.0];

    $stok = [];
    foreach ($komoditasStok as $kom) {
        foreach ($stokSeptBeras as $wil => $sept) {
            $rasio = $sept / end($stokBerasAcehUtara);
            $seri = array_map(fn($v) => round($v * $rasio * $faktorStok[$kom], 2), $stokBerasAcehUtara);
            $seri[count($seri) - 1] = round($sept * $faktorStok[$kom], 2); // September konsisten dengan distribusi
            $stok[$kom][$wil] = $seri;
        }
    }
    $wilayahStokList = array_keys($stokSeptBeras);
@endphp

<style>
    .dw-card { border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; }
    .sec-title { font-weight: 700; font-size: 1.5rem; }
    .dist-row { margin-bottom: .9rem; }
    .dist-row .head { display: flex; justify-content: space-between; font-size: .8rem; font-weight: 600; margin-bottom: .2rem; }
    .dist-row .progress { height: 9px; background: #e5eef6; }
    .dist-row .progress-bar { background: #0f9d8a; }
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
                <option value="{{ $t }}" {{ $t == $tahunAktif ? 'selected' : '' }}>{{ $t }}</option>
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
                <div class="text-muted small">Harga rata-rata per bulan (Rp/kg), dihitung dari data mingguan</div>
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
                <div class="text-muted small">Harga per kab/kota pada tanggal data terakhir ({{ $tanggalHarga }}), Rp/kg</div>
                <div id="chartBanding"></div>
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

        // ---------- 1. Tren harga ----------
        function optTren() {
            const data = trenHarga[$('selKomTren').value][$('selKabHarga').value];
            return {
                series: [{ name: 'Harga rata-rata', data: data }],
                xaxis: { categories: bulanHarga.map(b => b.substring(0, 3)), title: { text: 'Bulan ' + tahun, style: { fontSize: '11px' } } },
                yaxis: { title: { text: 'Harga (Rp/kg)', style: { fontSize: '11px' } }, labels: { formatter: v => fmt0(Math.round(v)) } },
                tooltip: { y: { formatter: v => rp(v) + '/kg' } }
            };
        }
        const chartTren = new ApexCharts($('chartTren'), Object.assign({
            chart: { type: 'line', height: 300, toolbar: { show: false } },
            stroke: { curve: 'straight', width: 2.5 },
            markers: { size: 4 },
            colors: ['#4f94d6'],
            dataLabels: { enabled: false },
            grid: { strokeDashArray: 3 }
        }, optTren()));
        chartTren.render();
        ['selKomTren', 'selKabHarga'].forEach(id => $(id).addEventListener('change', () => chartTren.updateOptions(optTren())));

        // ---------- 2. Perbandingan harga ----------
        function optBanding() {
            const rows = Object.entries(perbandingan[$('selKomBanding').value]).sort((a, b) => b[1] - a[1]);
            return {
                chart: { type: 'bar', height: rows.length * 26 + 40, toolbar: { show: false } },
                series: [{ name: 'Harga', data: rows.map(r => r[1]) }],
                xaxis: { categories: rows.map(r => r[0].replace('Kabupaten ', 'Kab. ')), labels: { formatter: v => fmt0(Math.round(v)) } },
                yaxis: { labels: { style: { fontSize: '10px' }, maxWidth: 140 } },
                plotOptions: { bar: { horizontal: true, barHeight: '70%', borderRadius: 2 } },
                colors: ['#9f1853'],
                dataLabels: { enabled: true, formatter: v => fmt0(v), textAnchor: 'start', offsetX: 6, style: { fontSize: '10px', colors: ['#0f172a'] }, background: { enabled: false } },
                tooltip: { y: { formatter: v => rp(v) + '/kg' } },
                grid: { strokeDashArray: 3 }
            };
        }
        const chartBanding = new ApexCharts($('chartBanding'), optBanding());
        chartBanding.render();
        $('selKomBanding').addEventListener('change', () => chartBanding.updateOptions(optBanding()));

        // ---------- 3. Tren ketersediaan stok ----------
        function optStok() {
            const data = stok[$('selKomStok').value][$('selKabStok').value];
            return {
                series: [{ name: 'Stok (ton)', data: data }],
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

        // ---------- 4. Distribusi wilayah (September, top 5) ----------
        function renderDistribusi() {
            const kom = $('selKomStok').value;
            const rows = Object.entries(stok[kom])
                .map(([wil, seri]) => [wil, seri[seri.length - 1]])
                .sort((a, b) => b[1] - a[1])
                .slice(0, 5);
            const max = rows[0][1];
            $('subDistribusi').textContent = 'Perbandingan jumlah ketersediaan stok ' + kom + ' (' + bulanStok[bulanStok.length - 1] + ' ' + tahun + ')';
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