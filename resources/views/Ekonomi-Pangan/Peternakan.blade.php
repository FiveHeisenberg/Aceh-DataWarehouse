@extends('layouts.app')

@section('content')
@php
    // ====== DATA STATIS ======
    $tahunList  = [2026, 2025, 2024];
    $tahunAktif = 2026;

    $satuanPopulasi = 'ekor';
    $satuanProduksi = 'ton';

    // Dropdown pada peta
    $jenisTernakList = ['Sapi', 'Ayam', 'Itik', 'Kambing', 'Kerbau', 'Domba'];

    // Komposisi populasi ternak (satu irisan = satu jenis ternak)
    $populasi = [
        ['label' => 'Ayam',    'jumlah' => 16200450, 'warna' => '#f59e0b'],
        ['label' => 'Itik',    'jumlah' => 1480320,  'warna' => '#14b8a6'],
        ['label' => 'Sapi',    'jumlah' => 780125,   'warna' => '#8b5cf6'],
        ['label' => 'Kambing', 'jumlah' => 340870,   'warna' => '#3b82f6'],
        ['label' => 'Kerbau',  'jumlah' => 98236,    'warna' => '#ef4444'],
        ['label' => 'Domba',   'jumlah' => 30000,    'warna' => '#06b6d4'],
    ];
    $totalPopulasi = array_sum(array_column($populasi, 'jumlah')); // 18.930.001

    // Produksi per produk (se-Aceh): satu batang = satu jenis ternak
    $produksiPerProduk = [
        'Daging' => [
            ['Sapi', 20707.58], ['Ayam', 14520.83], ['Kambing', 3476.48],
            ['Kerbau', 2816.02], ['Itik', 647.65],  ['Domba', 360.99],
        ],
        'Telur' => [
            ['Ayam', 9850.40], ['Itik', 2310.75],
        ],
        'Susu' => [
            ['Sapi', 1240.60], ['Kambing', 310.25],
        ],
    ];

    // Produksi per kabupaten/kota: satu batang = kombinasi produk + jenis ternak
    // urutan nilai: Telur Ayam, Daging Sapi, Daging Ayam, Daging Kambing, Telur Itik, Daging Kerbau, Daging Itik, Daging Domba
    $labelKombinasi = ['Telur Ayam', 'Daging Sapi', 'Daging Ayam', 'Daging Kambing', 'Telur Itik', 'Daging Kerbau', 'Daging Itik', 'Daging Domba'];
    $nilaiKabupaten = [
        'Kota Banda Aceh' => [279.37, 149.23, 126.69, 42.22, 40.24, 21.77, 5.66, 3.10],
        'Aceh Besar'      => [812.45, 1420.30, 960.12, 310.55, 188.20, 245.60, 48.90, 36.75],
        'Aceh Utara'      => [1105.80, 1685.40, 1240.25, 420.10, 302.45, 380.15, 95.30, 52.60],
        'Pidie'           => [734.20, 1310.75, 890.40, 365.85, 210.10, 295.40, 61.25, 40.20],
        'Bireuen'         => [690.55, 1155.90, 805.35, 298.70, 175.60, 260.80, 52.45, 31.15],
    ];
    $kabupatenList = array_keys($nilaiKabupaten);

    $fmt = fn($n) => number_format($n, 0, ',', '.');
@endphp

<style>
    .dw-card { border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; }
    .sec-title { font-weight: 700; font-size: 1.35rem; margin: 1.5rem 0 .75rem; }
    #map { height: 330px; background: #f8fafc; border-radius: .5rem; }
    .legend-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .45rem 1rem; font-size: .8rem; }
    .legend-item { display: flex; align-items: center; }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; margin-right: .5rem; flex: 0 0 10px; }
    .legend-item .nama { flex: 1; font-weight: 600; }
    .legend-item .nilai { color: #475569; font-variant-numeric: tabular-nums; }
    .chip { display: inline-flex; align-items: center; font-size: .72rem; margin-right: .9rem; }
    .chip i { width: 10px; height: 10px; border-radius: 2px; margin-right: .35rem; display: inline-block; }
</style>

<div class="container-fluid py-3">

    {{-- Breadcrumb + filter tahun --}}
    <div class="d-flex justify-content-between align-items-start mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Ekonomi Pangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Peternakan</li>
            </ol>
        </nav>
        <select id="filterTahun" class="form-select form-select-sm w-auto">
            @foreach ($tahunList as $t)
                <option value="{{ $t }}" {{ $t == $tahunAktif ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
    </div>

    <h2 class="fw-bold mb-3">Persebaran Populasi Ternak</h2>

    {{-- Peta + komposisi --}}
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="dw-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold mb-0">Peta Populasi Ternak</h5>
                    <select id="filterJenisTernak" class="form-select form-select-sm w-auto">
                        @foreach ($jenisTernakList as $j)
                            <option value="{{ $j }}">{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Bagian peta dikosongkan (siapkan untuk Leaflet choropleth) --}}
                <div id="map"></div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="dw-card p-3 h-100">
                <h5 class="fw-bold mb-2">Komposisi Populasi Ternak</h5>
                <div id="chartPopulasi"></div>
                <div class="legend-grid mt-2 px-2">
                    @foreach ($populasi as $p)
                        <div class="legend-item">
                            <span class="legend-dot" style="background: {{ $p['warna'] }}"></span>
                            <span class="nama">{{ $p['label'] }}</span>
                            <span class="nilai">{{ $fmt($p['jumlah']) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Produksi peternakan --}}
    <div class="sec-title">Produksi Peternakan</div>
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h5 class="fw-bold mb-0">Per Produk</h5>
                    <select id="selProduk" class="form-select form-select-sm w-auto">
                        @foreach (array_keys($produksiPerProduk) as $prd)
                            <option value="{{ $prd }}">{{ $prd }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-muted small mb-2">Total produksi <span id="lblProduk">daging</span> per jenis ternak se-Aceh ({{ $satuanProduksi }})</div>
                <div id="chartProduk"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h5 class="fw-bold mb-0">Per Kabupaten/Kota</h5>
                    <select id="selKabupaten" class="form-select form-select-sm w-auto">
                        @foreach ($kabupatenList as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="text-muted small mb-2">Produksi per kombinasi produk dan jenis ternak ({{ $satuanProduksi }})</div>
                <div class="mb-2">
                    <span class="chip"><i style="background:#9f1853"></i>Daging</span>
                    <span class="chip"><i style="background:#f59e0b"></i>Telur</span>
                    <span class="chip"><i style="background:#3b82f6"></i>Susu</span>
                </div>
                <div id="chartKabupaten"></div>
            </div>
        </div>
    </div>
</div>

{{-- ApexCharts (hapus jika layouts.app sudah memuatnya) --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const populasi        = @json($populasi);
        const totalPopulasi   = {{ $totalPopulasi }};
        const produksiProduk  = @json($produksiPerProduk);
        const labelKombinasi  = @json($labelKombinasi);
        const nilaiKabupaten  = @json($nilaiKabupaten);
        const satuanProduksi  = @json($satuanProduksi);

        const fmt0 = n => n.toLocaleString('id-ID');
        const fmt2 = n => n.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // ---------- Donut komposisi populasi ternak ----------
        new ApexCharts(document.querySelector('#chartPopulasi'), {
            chart: { type: 'donut', height: 290 },
            series: populasi.map(d => d.jumlah),
            labels: populasi.map(d => d.label),
            colors: populasi.map(d => d.warna),
            legend: { show: false },
            dataLabels: { enabled: false },
            tooltip: { y: { formatter: v => fmt0(v) + ' ekor' } },
            plotOptions: {
                pie: { donut: { size: '66%', labels: {
                    show: true,
                    name: { show: true, fontSize: '11px', fontWeight: 600, offsetY: 18 },
                    value: { show: true, fontSize: '20px', fontWeight: 700, offsetY: -14, formatter: v => fmt0(Number(v)) },
                    total: { show: true, showAlways: true, label: 'Total Ekor', fontSize: '11px', formatter: () => fmt0(totalPopulasi) }
                } } }
            }
        }).render();

        // ---------- Opsi umum bar horizontal ----------
        function barOptions(categories, values, colors) {
            return {
                chart: { type: 'bar', height: Math.max(260, categories.length * 42 + 40), toolbar: { show: false }, animations: { enabled: true } },
                series: [{ name: 'Produksi (' + satuanProduksi + ')', data: values }],
                xaxis: { categories: categories, labels: { formatter: v => fmt0(Math.round(v)) } },
                plotOptions: { bar: { horizontal: true, distributed: true, barHeight: '65%', borderRadius: 2 } },
                colors: colors,
                legend: { show: false },
                dataLabels: { enabled: true, formatter: v => fmt2(v), textAnchor: 'start', offsetX: 6, style: { fontSize: '11px', colors: ['#0f172a'] }, background: { enabled: false } },
                tooltip: { y: { formatter: v => fmt2(v) + ' ' + satuanProduksi } }
            };
        }

        // ---------- Per produk ----------
        const selProduk = document.getElementById('selProduk');
        const lblProduk = document.getElementById('lblProduk');
        const warnaProduk = { 'Daging': '#9f1853', 'Telur': '#f59e0b', 'Susu': '#3b82f6' };

        function optProduk() {
            const produk = selProduk.value;
            const rows = produksiProduk[produk].slice().sort((a, b) => b[1] - a[1]);
            lblProduk.textContent = produk.toLowerCase();
            return barOptions(rows.map(r => r[0]), rows.map(r => r[1]), rows.map(() => warnaProduk[produk]));
        }
        const chartProduk = new ApexCharts(document.querySelector('#chartProduk'), optProduk());
        chartProduk.render();
        selProduk.addEventListener('change', () => chartProduk.updateOptions(optProduk()));

        // ---------- Per kabupaten/kota ----------
        const selKab = document.getElementById('selKabupaten');
        const warnaJenis = { 'Daging': '#9f1853', 'Telur': '#f59e0b', 'Susu': '#3b82f6' };

        function optKabupaten() {
            const nilai = nilaiKabupaten[selKab.value];
            const rows = labelKombinasi.map((l, i) => ({ label: l, v: nilai[i] }))
                                       .sort((a, b) => b.v - a.v);
            return barOptions(
                rows.map(r => r.label),
                rows.map(r => r.v),
                rows.map(r => warnaJenis[r.label.split(' ')[0]])
            );
        }
        const chartKab = new ApexCharts(document.querySelector('#chartKabupaten'), optKabupaten());
        chartKab.render();
        selKab.addEventListener('change', () => chartKab.updateOptions(optKabupaten()));
    });
</script>
@endsection