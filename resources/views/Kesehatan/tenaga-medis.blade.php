@extends('layouts.app')

@section('content')
@php
    // ====== DATA STATIS ======
    $tahunList  = [2023, 2022, 2021];
    $tahunAktif = 2023;

    // Kartu ringkasan
    $kpi = [
        ['judul' => 'Total Tenaga Medis',   'nilai' => 12482, 'tren' => '+6,8%', 'ikon' => 'bi-people-fill',  'warna' => '#3b82f6', 'bg' => '#e8f1ff'],
        ['judul' => 'Tenaga Medis Dokter',  'nilai' => 2843,  'tren' => '+7,2%', 'ikon' => 'bi-heart-pulse',  'warna' => '#22a06b', 'bg' => '#e6f6ee'],
        ['judul' => 'Tenaga Medis Perawat', 'nilai' => 6521,  'tren' => '+5,9%', 'ikon' => 'bi-person-heart', 'warna' => '#8b5cf6', 'bg' => '#f0eaff'],
        ['judul' => 'Tenaga Medis Lainnya', 'nilai' => 3118,  'tren' => '+6,1%', 'ikon' => 'bi-person-badge', 'warna' => '#14b8a6', 'bg' => '#e3f7f4'],
    ];

    // Jumlah per kelompok
    $kelompok = [
        ['label' => 'Tenaga Keperawatan',        'jumlah' => 6521, 'warna' => '#3b82f6'],
        ['label' => 'Tenaga Kedokteran',         'jumlah' => 2843, 'warna' => '#22a06b'],
        ['label' => 'Tenaga Kefarmasian',        'jumlah' => 1102, 'warna' => '#8b7fd6'],
        ['label' => 'Tenaga Kesehatan Lainnya',  'jumlah' => 870,  'warna' => '#f5b82e'],
    ];

    // Distribusi per jenis
    $jenis = [
        ['nama' => 'Perawat',                   'jumlah' => 6521],
        ['nama' => 'Dokter Spesialis',          'jumlah' => 1342],
        ['nama' => 'Dokter Umum',               'jumlah' => 1201],
        ['nama' => 'Bidan',                     'jumlah' => 980],
        ['nama' => 'Apoteker',                  'jumlah' => 562],
        ['nama' => 'Analis Kesehatan',          'jumlah' => 423],
        ['nama' => 'Radiografer',               'jumlah' => 318],
        ['nama' => 'Tenaga Gizi',               'jumlah' => 276],
        ['nama' => 'Terapis',                   'jumlah' => 198],
        ['nama' => 'Tenaga Kesehatan Lainnya',  'jumlah' => 362],
    ];

    // Sebaran per kabupaten/kota
    $kabupaten = [
        ['nama' => 'Banda Aceh',   'jumlah' => 2184],
        ['nama' => 'Aceh Besar',   'jumlah' => 1762],
        ['nama' => 'Lhokseumawe',  'jumlah' => 1245],
        ['nama' => 'Langsa',       'jumlah' => 980],
        ['nama' => 'Bireuen',      'jumlah' => 876],
        ['nama' => 'Aceh Utara',   'jumlah' => 654],
        ['nama' => 'Aceh Tamiang', 'jumlah' => 593],
        ['nama' => 'Pidie',        'jumlah' => 521],
        ['nama' => 'Aceh Barat',   'jumlah' => 412],
        ['nama' => 'Sabang',       'jumlah' => 298],
    ];

    $maxJenis     = max(array_column($jenis, 'jumlah'));
    $maxKabupaten = max(array_column($kabupaten, 'jumlah'));

    // helper format angka Indonesia (12.482)
    $fmt = fn($n) => number_format($n, 0, ',', '.');
@endphp

<style>
    .dw-card { border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; }
    .dw-kpi { display: flex; align-items: center; gap: .9rem; border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; padding: .9rem 1.1rem; height: 100%; }
    .dw-kpi .icon { width: 48px; height: 48px; flex: 0 0 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
    .dw-kpi .title { font-weight: 600; font-size: .78rem; color: #1e293b; }
    .dw-kpi .value { font-weight: 800; font-size: 1.7rem; line-height: 1.15; color: #0f172a; }
    .dw-kpi .unit { font-size: .7rem; color: #64748b; font-weight: 400; margin-left: .15rem; }
    .dw-kpi .trend { font-size: .72rem; color: #64748b; }
    .dw-kpi .trend .up { color: #16a34a; font-weight: 600; }
    .bar-row { display: flex; align-items: center; gap: .6rem; margin-bottom: .45rem; font-size: .78rem; }
    .bar-row .name { width: 130px; flex: 0 0 130px; color: #1e293b; }
    .bar-row .track { flex: 1; height: 11px; background: #eef2f7; border-radius: 3px; overflow: hidden; }
    .bar-row .fill { height: 100%; background: #7a98c0; border-radius: 3px; }
    .bar-row .val { width: 46px; flex: 0 0 46px; text-align: right; color: #334155; font-variant-numeric: tabular-nums; }
    .table-medis thead th { background: #e5e7eb; text-align: center; font-weight: 600; }
    .table-medis tbody td { padding-top: .6rem; padding-bottom: .6rem; }
</style>

<div class="container-fluid py-3">

    {{-- Breadcrumb + filter tahun --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Kesehatan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tenaga Medis</li>
            </ol>
        </nav>
        <select id="filterTahun" class="form-select form-select-sm w-auto">
            @foreach ($tahunList as $t)
                <option value="{{ $t }}" {{ $t == $tahunAktif ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
    </div>

    {{-- Kartu ringkasan --}}
    <div class="row g-3 mb-3">
        @foreach ($kpi as $k)
            <div class="col-sm-6 col-xl-3">
                <div class="dw-kpi">
                    <div class="icon" style="background: {{ $k['bg'] }}; color: {{ $k['warna'] }}">
                        <i class="bi {{ $k['ikon'] }}"></i>
                    </div>
                    <div>
                        <div class="title">{{ $k['judul'] }}</div>
                        <div class="value">{{ $fmt($k['nilai']) }}<span class="unit">orang</span></div>
                        <div class="trend">
                            <span class="up"><i class="bi bi-caret-up-fill"></i> {{ $k['tren'] }}</span>
                            vs {{ $tahunAktif + 1 }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tiga grafik --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-4">
            <div class="dw-card p-3 h-100">
                <h6 class="fw-bold mb-2">Jumlah Tenaga Medis per Kelompok</h6>
                <div id="chartKelompok"></div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="dw-card p-3 h-100">
                <h6 class="fw-bold mb-3">Distribusi Tenaga Medis per Jenis</h6>
                @foreach ($jenis as $j)
                    <div class="bar-row">
                        <div class="name">{{ $j['nama'] }}</div>
                        <div class="track"><div class="fill" style="width: {{ round($j['jumlah'] / $maxJenis * 100, 1) }}%"></div></div>
                        <div class="val">{{ $fmt($j['jumlah']) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-xl-4">
            <div class="dw-card p-3 h-100">
                <h6 class="fw-bold mb-3">Sebaran Tenaga Medis per Kabupaten/Kota</h6>
                @foreach ($kabupaten as $k)
                    <div class="bar-row">
                        <div class="name">{{ $k['nama'] }}</div>
                        <div class="track"><div class="fill" style="width: {{ round($k['jumlah'] / $maxKabupaten * 100, 1) }}%"></div></div>
                        <div class="val">{{ $fmt($k['jumlah']) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="table-responsive mb-4">
        <table class="table table-medis table-bordered border-start-0 border-end-0 align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 80px">No</th>
                    <th>Jenis Tenaga Medis</th>
                    <th style="width: 220px">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jenis as $i => $j)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $j['nama'] }}</td>
                        <td class="text-center">{{ $fmt($j['jumlah']) }}</td>
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
        const dataKelompok = @json($kelompok);
        const rupiah = n => n.toLocaleString('id-ID');

        // ---------- Kolom jumlah per kelompok ----------
        new ApexCharts(document.querySelector('#chartKelompok'), {
            chart: { type: 'bar', height: 260, toolbar: { show: false } },
            series: [{ name: 'Jumlah', data: dataKelompok.map(d => d.jumlah) }],
            xaxis: {
                categories: dataKelompok.map(d => d.label),
                labels: { style: { fontSize: '10px' }, trim: true, rotate: 0, hideOverlappingLabels: false }
            },
            yaxis: { min: 0, max: 8000, tickAmount: 4, labels: { formatter: v => rupiah(v) } },
            plotOptions: { bar: { distributed: true, borderRadius: 4, columnWidth: '50%' } },
            colors: dataKelompok.map(d => d.warna),
            legend: { show: false },
            dataLabels: { enabled: true, formatter: v => rupiah(v), offsetY: -18, style: { fontSize: '11px', colors: ['#0f172a'] } },
            tooltip: { y: { formatter: v => rupiah(v) + ' orang' } }
        }).render();
    });
</script>
@endsection