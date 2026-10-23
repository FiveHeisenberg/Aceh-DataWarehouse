@extends('layouts.app')

@section('content')
@php
    // ====== DATA STATIS ======

    $totalKasus = 12482;

    $penyakit = [
        ['nama' => 'ISPA',            'jumlah' => 2843, 'kabupaten' => 'Kota Banda Aceh'],
        ['nama' => 'Diare',           'jumlah' => 1762, 'kabupaten' => 'Kabupaten Aceh Besar'],
        ['nama' => 'DBD',             'jumlah' => 1245, 'kabupaten' => 'Kota Lhokseumawe'],
        ['nama' => 'Hipertensi',      'jumlah' => 980,  'kabupaten' => 'Kabupaten Aceh Utara'],
        ['nama' => 'Diabetes Melitus','jumlah' => 762,  'kabupaten' => 'Kota Banda Aceh'],
        ['nama' => 'Tifoid',          'jumlah' => 584,  'kabupaten' => 'Kabupaten Bireuen'],
        ['nama' => 'Malaria',         'jumlah' => 421,  'kabupaten' => 'Kabupaten Aceh Tamiang'],
        ['nama' => 'TB Paru',         'jumlah' => 398,  'kabupaten' => 'Kota Langsa'],
        ['nama' => 'Campak',          'jumlah' => 312,  'kabupaten' => 'Kabupaten Pidie'],
        ['nama' => 'Hepatitis',       'jumlah' => 276,  'kabupaten' => 'Kabupaten Aceh Barat'],
    ];

    $kabupaten = [
        ['nama' => 'Banda Aceh',  'jumlah' => 1982],
        ['nama' => 'Aceh Besar',  'jumlah' => 1543],
        ['nama' => 'Lhokseumawe', 'jumlah' => 1267],
        ['nama' => 'Langsa',      'jumlah' => 1102],
        ['nama' => 'Bireuen',     'jumlah' => 987],
        ['nama' => 'Aceh Utara',  'jumlah' => 876],
        ['nama' => 'Aceh Tamiang','jumlah' => 654],
        ['nama' => 'Pidie',       'jumlah' => 593],
        ['nama' => 'Aceh Barat',  'jumlah' => 521],
        ['nama' => 'Sabang',      'jumlah' => 412],
    ];

    $usia = [
        ['label' => '0 – 14 tahun',  'jumlah' => 3768, 'persen' => '30,2%', 'warna' => '#3b82f6'],
        ['label' => '15 – 29 tahun', 'jumlah' => 3081, 'persen' => '24,7%', 'warna' => '#22a06b'],
        ['label' => '30 – 44 tahun', 'jumlah' => 2510, 'persen' => '20,1%', 'warna' => '#f5b82e'],
        ['label' => '45 – 59 tahun', 'jumlah' => 1910, 'persen' => '15,3%', 'warna' => '#e8895a'],
        ['label' => '60+ tahun',     'jumlah' => 1213, 'persen' => '9,7%',  'warna' => '#8b7fd6'],
    ];

    $gender = [
        'laki'      => ['jumlah' => 5982, 'persen' => '47,9%'],
        'perempuan' => ['jumlah' => 6500, 'persen' => '52,1%'],
    ];

    $ringkasan = array_slice($penyakit, 0, 4);

    $maxPenyakit   = max(array_column($penyakit, 'jumlah'));
    $maxKabupaten  = max(array_column($kabupaten, 'jumlah'));

    // helper format angka Indonesia (12.482)
    $fmt = fn($n) => number_format($n, 0, ',', '.');
@endphp

<style>
    .dw-card { border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; }
    .dw-kpi { display: flex; align-items: center; gap: .9rem; border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; padding: .9rem 1.1rem; }
    .dw-kpi .icon { width: 52px; height: 52px; flex: 0 0 52px; border-radius: .6rem; background: #f1f5f9; border: 1px solid #d1d5db; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; }
    .dw-kpi .title { font-weight: 600; font-size: .95rem; color: #1e293b; }
    .dw-kpi .value { font-weight: 800; font-size: 1.9rem; line-height: 1.1; color: #0f172a; }
    .dw-kpi .desc { font-size: .78rem; color: #475569; }
    #map { min-height: 100%; height: 100%; background: #f8fafc; border-radius: .6rem; }
    .map-wrap { height: 100%; min-height: 330px; }
    .bar-row { display: flex; align-items: center; gap: .6rem; margin-bottom: .45rem; font-size: .8rem; }
    .bar-row .name { width: 120px; flex: 0 0 120px; color: #1e293b; }
    .bar-row .track { flex: 1; height: 12px; background: #eef2f7; border-radius: 3px; overflow: hidden; }
    .bar-row .fill { height: 100%; background: #7a98c0; border-radius: 3px; }
    .bar-row .val { width: 48px; flex: 0 0 48px; text-align: right; color: #334155; font-variant-numeric: tabular-nums; }
    .legend-item { display: flex; align-items: center; font-size: .85rem; margin-bottom: .55rem; }
    .legend-dot { width: 12px; height: 12px; border-radius: 50%; margin-right: .6rem; flex: 0 0 12px; }
    .gender-box { display: flex; justify-content: space-around; border-top: 1px solid #e5e7eb; padding-top: .8rem; margin-top: .4rem; background: #f8fafc; border-radius: .5rem; padding-bottom: .6rem; }
    .gender-box .pct { font-weight: 700; font-size: 1.1rem; }
    .gender-box .lbl { font-size: .75rem; color: #64748b; }
    .table-summary thead th { background: #e5e7eb; text-align: center; font-weight: 700; }
    .table-summary tbody td { padding-top: .9rem; padding-bottom: .9rem; }
</style>

<div class="container-fluid py-3">

    {{-- Breadcrumb + filter tahun --}}
    <div class="d-flex justify-content-between align-items-start mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Kesehatan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penyakit</li>
            </ol>
        </nav>
        <form method="GET" action="{{ route('kesehatan.penyakit') }}">
            <label for="filterTahun" class="visually-hidden">Pilih tahun</label>
            <select
                id="filterTahun"
                name="tahun"
                class="form-select form-select-sm w-auto"
                onchange="this.form.submit()"
            >
                @foreach ($tahunList as $tahun)
                    <option value="{{ $tahun }}" @selected($tahun === $tahunAktif)>
                        {{ $tahun }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <h2 class="fw-bold mb-0">Analisis Penyakit</h2>
    <p class="mb-3">Ringkasan dan persebaran data penyakit di Aceh</p>

    {{-- Peta + KPI --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="dw-card p-2 map-wrap">
                {{-- Bagian peta dikosongkan (siapkan untuk Leaflet choropleth) --}}
                <div id="map"></div>
            </div>
        </div>
        <div class="col-lg-4 d-flex flex-column gap-3">
            <div class="dw-kpi">
                <div class="icon"><i class="bi bi-emoji-dizzy"></i></div>
                <div>
                    <div class="title">Total Kasus Penyakit</div>
                    <div class="value">{{ $fmt($totalKasus) }}</div>
                    <div class="desc">dari seluruh kabupaten/kota</div>
                </div>
            </div>
            <div class="dw-kpi">
                <div class="icon"><i class="bi bi-virus"></i></div>
                <div>
                    <div class="title">Penyakit Terbanyak</div>
                    <div class="value">{{ $penyakit[0]['nama'] }}</div>
                    <div class="desc">{{ $fmt($penyakit[0]['jumlah']) }} kasus</div>
                </div>
            </div>
            <div class="dw-kpi">
                <div class="icon"><i class="bi bi-emoji-dizzy"></i></div>
                <div>
                    <div class="title">Kabupaten/Kota Terbanyak</div>
                    <div class="value">{{ $kabupaten[0]['nama'] }}</div>
                    <div class="desc">{{ $fmt($kabupaten[0]['jumlah']) }} Kasus</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 10 penyakit terbanyak & kasus per kabupaten --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <h6 class="fw-bold mb-3">10 Penyakit Terbanyak</h6>
                @foreach ($penyakit as $p)
                    <div class="bar-row">
                        <div class="name">{{ $p['nama'] }}</div>
                        <div class="track"><div class="fill" style="width: {{ round($p['jumlah'] / $maxPenyakit * 100, 1) }}%"></div></div>
                        <div class="val">{{ $fmt($p['jumlah']) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <h6 class="fw-bold mb-3">Kasus Penyakit per Kabupaten/Kota</h6>
                @foreach ($kabupaten as $i => $k)
                    <div class="bar-row">
                        <div class="name">{{ $i + 1 }}. {{ $k['nama'] }}</div>
                        <div class="track"><div class="fill" style="width: {{ round($k['jumlah'] / $maxKabupaten * 100, 1) }}%"></div></div>
                        <div class="val">{{ $fmt($k['jumlah']) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Distribusi usia & jenis kelamin --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <h6 class="fw-bold mb-3">Distribusi Kasus berdasarkan Kelompok Usia</h6>
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div id="chartUsia"></div>
                    </div>
                    <div class="col-md-6">
                        @foreach ($usia as $u)
                            <div class="legend-item">
                                <span class="legend-dot" style="background: {{ $u['warna'] }}"></span>
                                <span class="flex-grow-1">{{ $u['label'] }}</span>
                                <span class="text-nowrap">{{ $fmt($u['jumlah']) }} ({{ $u['persen'] }})</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <h6 class="fw-bold mb-2">Kasus berdasarkan Jenis Kelamin</h6>
                <div id="chartGender"></div>
                <div class="gender-box">
                    <div class="text-center">
                        <i class="bi bi-person-standing text-primary fs-4"></i>
                        <div class="pct">{{ $gender['laki']['persen'] }}</div>
                        <div class="lbl">Laki-laki</div>
                    </div>
                    <div class="text-center">
                        <i class="bi bi-person-standing-dress fs-4"></i>
                        <div class="pct">{{ $gender['perempuan']['persen'] }}</div>
                        <div class="lbl">Perempuan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan persebaran penyakit --}}
    <h4 class="fw-bold mb-3">Ringkasan Persebaran Penyakit</h4>
    <div class="table-responsive mb-4">
        <table class="table table-summary align-middle mb-0">
            <thead>
                <tr>
                    <th>Jenis Penyakit</th>
                    <th>Jumlah Kasus</th>
                    <th>Kabupaten/Kota Tertinggi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ringkasan as $r)
                    <tr>
                        <td>{{ $r['nama'] }}</td>
                        <td class="text-center">{{ $fmt($r['jumlah']) }}</td>
                        <td>{{ $r['kabupaten'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Bootstrap Icons & ApexCharts (hapus jika layouts.app sudah memuatnya) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dataUsia = @json($usia);
        const total    = {{ $totalKasus }};
        const rupiah   = n => n.toLocaleString('id-ID');

        // ---------- Donut kelompok usia ----------
        new ApexCharts(document.querySelector('#chartUsia'), {
            chart: { type: 'donut', height: 240 },
            series: dataUsia.map(d => d.jumlah),
            labels: dataUsia.map(d => d.label),
            colors: dataUsia.map(d => d.warna),
            legend: { show: false },
            dataLabels: {
                enabled: true,
                formatter: (v) => v.toFixed(1).replace('.', ',') + '%'
            },
            tooltip: { y: { formatter: v => rupiah(v) + ' kasus' } },
            plotOptions: {
                pie: { donut: { size: '58%', labels: {
                    show: true,
                    name: { show: false },
                    value: { show: true, fontSize: '20px', fontWeight: 700, offsetY: 4 },
                    total: { show: true, label: 'kasus', formatter: () => rupiah(total) }
                } } }
            }
        }).render();

        // ---------- Bar jenis kelamin ----------
        new ApexCharts(document.querySelector('#chartGender'), {
            chart: { type: 'bar', height: 240, toolbar: { show: false } },
            series: [{ name: 'Kasus', data: [{{ $gender['laki']['jumlah'] }}, {{ $gender['perempuan']['jumlah'] }}] }],
            xaxis: { categories: ['Laki-laki', 'Perempuan'] },
            yaxis: { min: 0, max: 8000, tickAmount: 4, labels: { formatter: v => rupiah(v) } },
            plotOptions: { bar: { distributed: true, borderRadius: 4, columnWidth: '40%' } },
            colors: ['#3b82f6', '#22a06b'],
            legend: { show: false },
            dataLabels: { enabled: true, formatter: v => rupiah(v), offsetY: -18, style: { colors: ['#0f172a'] } },
            tooltip: { y: { formatter: v => rupiah(v) + ' kasus' } }
        }).render();
    });
</script>
@endsection