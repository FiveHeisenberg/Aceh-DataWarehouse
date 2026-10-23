@extends('layouts.app')

@section('content')
@php
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

    .chart-kelompok-card {
        min-width: 0;        /* mencegah card melebar mengikuti isi */
        max-width: 100%;
        overflow: hidden;    /* isi yang lebar tidak keluar dari card */
    }

    #chartKelompok {
        width: 100%;         /* lebar akhir diatur dari JavaScript */
    }

    .chart-kelompok-col {
        min-width: 0;   /* kolom tidak boleh lebih lebar dari row */
        max-width: 100%;
    }

    .chart-kelompok-scroll {
        width: 100%;
        min-width: 0;
        overflow-x: auto;
        overflow-y: hidden;
        contain: inline-size; /* lebar wrapper tidak dihitung dari isi chart yang lebar */
        padding-bottom: .5rem;
    }

    .jenis-medis-scroll {
        max-height: 280px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: .4rem;
    }

    .jenis-medis-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .jenis-medis-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

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
        <form method="GET" action="{{ route('kesehatan.tenaga-medis') }}">
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
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Dua grafik --}}
    <div class="row g-3 pb-3">
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <h6 class="fw-bold mb-3">Distribusi Tenaga Medis per Jenis</h6>
                <div class="jenis-medis-scroll">
                    @foreach ($jenis as $j)
                        <div class="bar-row">
                            <div class="name">{{ $j['nama'] }}</div>
                            <div class="track"><div class="fill" style="width: {{ $maxJenis > 0 ? round(($j['jumlah'] / $maxJenis) * 100, 1) : 0 }}%"></div></div>
                            <div class="val">{{ $fmt($j['jumlah']) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <h6 class="fw-bold mb-3">Sebaran Tenaga Medis per Kabupaten/Kota</h6>
                <div class="jenis-medis-scroll">
                    @foreach ($kabupaten as $k)
                        <div class="bar-row">
                            <div class="name">{{ $k['nama'] }}</div>
                            <div class="track"><div class="fill" style="width: {{ $maxKabupaten > 0 ? round(($k['jumlah'] / $maxKabupaten) * 100, 1) : 0 }}%"></div></div>
                            <div class="val">{{ $fmt($k['jumlah']) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 chart-kelompok-col">
            <div class="dw-card p-3 h-100 chart-kelompok-card">
                <h6 class="fw-bold mb-2">Jumlah Tenaga Medis per Kelompok</h6>
                <div class="chart-kelompok-scroll">
                    <div id="chartKelompok"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <h4 class="fw-bold mb-3">Persebaran Tenaga Medis Berdasarkan Profesi</h4>
    <div class="table-responsive mb-4">
        <table class="table table-medis table-bordered border-start-0 border-end-0 align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 80px">No</th>
                    <th>Kabupaten/Kota</th>
                    <th>Dokter</th>
                    <th>Perawat</th>
                    <th>Lainnya</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($profesiKabupaten as $i => $j)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $j['kabupaten'] }}</td>
                        <td class="text-center">{{ $fmt($j['dokter']) }}</td>
                        <td class="text-center">{{ $fmt($j['perawat']) }}</td>
                        <td class="text-center">{{ $fmt($j['lainnya']) }}</td>
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
        const kelompokValues = dataKelompok.map(item => item.jumlah);
        const kelompokMax = Math.max(...kelompokValues, 0);
        const kelompokAxisMax = kelompokMax > 0 ? Math.ceil(kelompokMax * 1.2 / 1000) * 1000 : 1000;

        // Lebar chart mengikuti jumlah kelompok, minimal 850px
        const lebarMinimal = 850;
        const lebarPerKelompok = 80;
        const lebarChart = Math.max(lebarMinimal, dataKelompok.length * lebarPerKelompok);

        const elChart = document.querySelector('#chartKelompok');
        elChart.style.width = lebarChart + 'px';

        new ApexCharts(elChart, {
            chart: {
                type: 'bar',
                height: 260,
                width: lebarChart,   // lebar eksplisit agar ApexCharts tidak mengecilkan batang
                toolbar: { show: false }
            },
            series: [{ name: 'Jumlah', data: kelompokValues }],
            xaxis: {
                categories: dataKelompok.map(d => d.label),
                labels: { 
                    style: { fontSize: '10px' }, 
                    trim: false, 
                    rotate: -50,
                    rotateAlways: true, 
                    hideOverlappingLabels: false 
                }
            },
            yaxis: {
                min: 0,
                max: kelompokAxisMax,
                tickAmount: 4,
                labels: { formatter: v => rupiah(v) }
            },
            plotOptions: { bar: { distributed: true, borderRadius: 4, columnWidth: '50%' } },
            colors: dataKelompok.map(d => d.warna),
            legend: { show: false },
            dataLabels: {
                enabled: true,
                formatter: v => rupiah(v),
                offsetY: -18,
                style: { fontSize: '11px', colors: ['#0f172a'] }
            },
            tooltip: { y: { formatter: v => rupiah(v) + ' orang' } }
        }).render();
    });
</script>
@endsection