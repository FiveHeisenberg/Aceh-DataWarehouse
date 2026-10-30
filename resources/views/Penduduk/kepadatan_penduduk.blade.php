@extends('layouts.app')

@section('content')
@php
    // =====================================================================
    // DATA STATIS
    // =====================================================================
    $tahunList  = [2026, 2025, 2024];
    $tahunAktif = 2026;

    // Tren kepadatan provinsi (jiwa/km²)
    $tahunTren = [2020, 2021, 2022, 2023, 2024, 2025];
    $trenAceh  = [82.1, 85.6, 88.9, 91.7, 94.2, 96.4];   // rata-rata Aceh
    $trenKab   = [56.3, 59.1, 62.8, 67.2, 71.5, 75.8];   // rata-rata kab/kota

    // Ringkasan (dihitung dari data tren)
    $kepadatanAceh   = end($trenAceh);
    $tahunPembanding = $tahunTren[count($tahunTren) - 2];
    $perubahanAceh   = (end($trenAceh) / $trenAceh[count($trenAceh) - 2] - 1) * 100;
    $kenaikanAceh    = (end($trenAceh) / $trenAceh[0] - 1) * 100;
    $kenaikanKab     = (end($trenKab) / $trenKab[0] - 1) * 100;

    // Peringkat kepadatan (jiwa/km²)
    $tertinggi = [
        ['Kota Banda Aceh', 6248], ['Kota Lhokseumawe', 5732], ['Kota Langsa', 5189],
        ['Kabupaten Aceh Tamiang', 3216], ['Kabupaten Aceh Besar', 2857], ['Kota Sabang', 2614],
        ['Kabupaten Bireuen', 2345], ['Kabupaten Pidie', 2213], ['Kabupaten Aceh Utara', 2086],
        ['Kabupaten Aceh Jaya', 1985],
    ];
    $terendah = [
        ['Kabupaten Aceh Singkil', 18], ['Kabupaten Gayo Lues', 21], ['Kota Subulussalam', 27],
        ['Kabupaten Aceh Tenggara', 33], ['Kabupaten Aceh Jaya', 36], ['Kabupaten Simeulue', 41],
        ['Kabupaten Aceh Selatan', 46], ['Kabupaten Nagan Raya', 52], ['Kabupaten Aceh Tengah', 58],
        ['Kabupaten Bener Meriah', 63],
    ];
    $wilayahTertinggi = $tertinggi[0];
    $wilayahTerendah  = $terendah[0];

    // Perbandingan jumlah penduduk, luas wilayah, dan kepadatan (data contoh)
    $banding = [
        ['nama' => 'Kota Banda Aceh', 'penduduk' => 439859, 'luas' => 70.4, 'kepadatan' => 6248],
        ['nama' => 'Kota Lhokseumawe', 'penduduk' => 215000, 'luas' => 37.5, 'kepadatan' => 5732],
        ['nama' => 'Kota Langsa', 'penduduk' => 210000, 'luas' => 40.5, 'kepadatan' => 5189],
        ['nama' => 'Kab. Aceh Besar', 'penduduk' => 200000, 'luas' => 70.0, 'kepadatan' => 2857],
        ['nama' => 'Kab. Pidie', 'penduduk' => 175000, 'luas' => 79.1, 'kepadatan' => 2213],
    ];

    $fmt0 = fn($n) => number_format($n, 0, ',', '.');
    $fmt1 = fn($n) => number_format($n, 1, ',', '.');
@endphp

<style>
    .dw-card { border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; }
    .sec-title { font-weight: 800; font-size: 1.5rem; margin: 1.75rem 0 .75rem; }
    #map { height: 290px; background: #f8fafc; border-radius: .5rem; }

    .kpi { border-radius: .75rem; padding: .9rem 1.1rem; height: 100%; display: flex; gap: .9rem; align-items: center; }
    .kpi .ic { width: 44px; height: 44px; flex: 0 0 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #fff; }
    .kpi .lbl { font-size: .72rem; font-weight: 700; color: #1e293b; }
    .kpi .big { font-size: 1.9rem; font-weight: 800; line-height: 1.1; color: #0f172a; }
    .kpi .unit { font-size: .7rem; color: #64748b; margin-left: .2rem; }
    .kpi .sub { font-size: .68rem; color: #94a3b8; }
    .kpi .up { color: #16a34a; font-weight: 700; font-size: .8rem; }
    .kpi.biru  { background: #eaf2ff; border: 1px solid #cfe0fb; }
    .kpi.biru .ic  { background: #4a8df0; }
    .kpi.merah { background: #fdeeee; border: 1px solid #f5cfcf; }
    .kpi.merah .ic { background: #ef5b5b; }
    .kpi.hijau { background: #eaf9f1; border: 1px solid #c4ead5; }
    .kpi.hijau .ic { background: #31b57a; }
    .kpi .wil-ic { font-size: 1.6rem; }
    .kpi.merah .wil-ic { color: #ef5b5b; }
    .kpi.hijau .wil-ic { color: #1f9d63; }
    .kpi .wil { font-size: .85rem; font-weight: 700; color: #0f172a; }
    .kpi .wil-val { font-size: .8rem; font-weight: 700; color: #0f172a; }

    .rank-card { background: #f4f7fc; border-radius: .75rem; padding: .9rem; height: 100%; }
    .rank-inner { background: #fff; border-radius: .6rem; padding: .85rem 1rem; height: 100%; box-shadow: 0 1px 4px rgba(15, 23, 42, .06); }
    .rank-head { display: flex; align-items: center; justify-content: space-between; gap: .5rem; margin-bottom: .75rem; }
    .rank-head .judul { font-size: .85rem; font-weight: 700; color: #0f172a; }
    .toggle-rank .btn { font-size: .7rem; padding: .2rem .7rem; }
    .toggle-rank .btn.active { background: #2f6fe4; border-color: #2f6fe4; color: #fff; }
    .rank-sub { font-size: .72rem; font-weight: 700; margin-bottom: .6rem; }
    .rank-row { display: flex; align-items: center; gap: .5rem; font-size: .72rem; margin-bottom: .5rem; }
    .rank-row .no { width: 18px; text-align: right; color: #475569; }
    .rank-row .nama { width: 140px; flex: 0 0 140px; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .rank-row .track { flex: 1; height: 10px; background: #eef2f8; border-radius: 3px; overflow: hidden; }
    .rank-row .fill { height: 100%; border-radius: 3px; transition: width .4s ease; }
    .rank-row .val { width: 42px; text-align: right; font-weight: 600; color: #334155; font-variant-numeric: tabular-nums; }

    .chart-wrap { background: #fff; border: 1px solid #e5e7eb; border-radius: .75rem; padding: 1rem; box-shadow: 0 1px 5px rgba(15, 23, 42, .05); }
    .stat-tren { background: #fff; border-radius: .6rem; padding: 1rem 1.1rem; }
    .stat-tren .lbl { font-size: .82rem; color: #334155; }
    .stat-tren .val { font-size: 1.9rem; font-weight: 800; color: #2f4fd8; line-height: 1.2; }
    .stat-tren .sub { font-size: .72rem; color: #94a3b8; }
</style>

<div class="container-fluid py-3">

    {{-- Breadcrumb + filter tahun --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Penduduk</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kepadatan Penduduk</li>
            </ol>
        </nav>
        <select id="filterTahun" class="form-select form-select-sm w-auto">
            @foreach ($tahunList as $t)
                <option value="{{ $t }}" {{ $t == $tahunAktif ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
    </div>

    {{-- Kartu ringkasan --}}
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="kpi biru">
                <div class="ic"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="lbl">Kepadatan Penduduk Rata-rata Aceh</div>
                    <div><span class="big">{{ $fmt1($kepadatanAceh) }}</span><span class="unit">jiwa/km²</span></div>
                    <div>
                        <span class="up"><i class="bi bi-arrow-up"></i> {{ $fmt1($perubahanAceh) }}%</span>
                        <span class="sub ms-1">dibandingkan tahun {{ $tahunPembanding }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="kpi merah">
                <div class="ic"><i class="bi bi-arrow-up"></i></div>
                <div>
                    <div class="lbl mb-1">Wilayah dengan Kepadatan Tertinggi</div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-buildings wil-ic"></i>
                        <div>
                            <div class="wil">{{ $wilayahTertinggi[0] }}</div>
                            <div class="wil-val">{{ $fmt0($wilayahTertinggi[1]) }} <span class="unit">jiwa/km²</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="kpi hijau">
                <div class="ic"><i class="bi bi-arrow-down"></i></div>
                <div>
                    <div class="lbl mb-1">Wilayah dengan Kepadatan Terendah</div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-tree wil-ic"></i>
                        <div>
                            <div class="wil">{{ $wilayahTerendah[0] }}</div>
                            <div class="wil-val">{{ $fmt0($wilayahTerendah[1]) }} <span class="unit">jiwa/km²</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Peta + peringkat --}}
    <div class="sec-title">Peta Sebaran Tingkat Kepadatan Penduduk</div>
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="dw-card p-2 h-100">
                {{-- Bagian peta dikosongkan (siapkan untuk Leaflet choropleth) --}}
                <div id="map"></div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="rank-card">
                <div class="rank-inner">
                    <div class="rank-head">
                        <div class="judul"><i class="bi bi-bar-chart-fill text-primary me-1"></i> Peringkat Kepadatan Penduduk</div>
                        <div class="btn-group toggle-rank" role="group" id="toggleRank">
                            <button type="button" class="btn btn-outline-primary active" data-mode="tertinggi">Tertinggi</button>
                            <button type="button" class="btn btn-outline-primary" data-mode="terendah">Terendah</button>
                        </div>
                    </div>
                    <div class="rank-sub" id="rankSub"></div>
                    <div id="rankList"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Perbandingan --}}
    <div class="sec-title">Perbandingan Jumlah Penduduk dan Luas Wilayah</div>
    <div class="chart-wrap">
        <div id="chartBanding"></div>
    </div>

    {{-- Tren --}}
    <div class="sec-title">Tren Perkembangan Kepadatan Penduduk Provinsi</div>
    <div class="row g-3 mb-4">
        <div class="">
            <div class="chart-wrap h-100">
                <div class="small text-muted">jiwa/km²</div>
                <div id="chartTren"></div>
            </div>
        </div>
    </div>
</div>

{{-- Bootstrap Icons & ApexCharts (hapus jika layouts.app sudah memuatnya) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tertinggi = @json($tertinggi);
        const terendah  = @json($terendah);
        const banding   = @json($banding);
        const tahunTren = @json($tahunTren);
        const trenAceh  = @json($trenAceh);
        const trenKab   = @json($trenKab);

        const fmt0 = n => Number(n).toLocaleString('id-ID');
        const fmt1 = n => Number(n).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });

        // ---------- Peringkat kepadatan (Tertinggi / Terendah) ----------
        const warnaBar = ['#2b44c9', '#2f52d6', '#3361e0', '#3a72e8', '#4281ee', '#4d90f2', '#5a9ef5', '#69aaf6', '#7bb6f7', '#8ec3f8'];
        const rankList = document.getElementById('rankList');
        const rankSub  = document.getElementById('rankSub');

        function renderRank(mode) {
            const rows = mode === 'tertinggi' ? tertinggi : terendah;
            const maks = Math.max(...rows.map(r => r[1]));
            rankSub.textContent = '10 Kabupaten/Kota dengan Kepadatan ' + (mode === 'tertinggi' ? 'Tertinggi' : 'Terendah');
            rankList.innerHTML = rows.map((r, i) => `
                <div class="rank-row">
                    <span class="no">${i + 1}.</span>
                    <span class="nama" title="${r[0]}">${r[0]}</span>
                    <div class="track"><div class="fill" style="width:${(r[1] / maks * 100).toFixed(1)}%;background:${warnaBar[i]}"></div></div>
                    <span class="val">${fmt0(r[1])}</span>
                </div>`).join('');
        }
        document.querySelectorAll('#toggleRank .btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#toggleRank .btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                renderRank(this.dataset.mode);
            });
        });
        renderRank('tertinggi');

        // ---------- Perbandingan penduduk, luas wilayah, kepadatan ----------
        new ApexCharts(document.querySelector('#chartBanding'), {
            chart: { type: 'line', height: 380, toolbar: { show: false } },
            series: [
                { name: 'Jumlah Penduduk', type: 'column', data: banding.map(b => b.penduduk) },
                { name: 'Luas Wilayah',    type: 'column', data: banding.map(b => b.luas) },
                { name: 'Kepadatan Penduduk', type: 'line', data: banding.map(b => b.kepadatan) }
            ],
            colors: ['#2f6fe4', '#f59e42', '#5b4fe0'],
            stroke: { width: [0, 0, 2.5], curve: 'smooth' },
            markers: { size: [0, 0, 4], strokeWidth: 2, strokeColors: '#fff' },
            plotOptions: { bar: { columnWidth: '55%', borderRadius: 2 } },
            dataLabels: { enabled: false },
            xaxis: { categories: banding.map(b => b.nama), labels: { style: { fontSize: '11px' } } },
            yaxis: [
                { seriesName: 'Jumlah Penduduk', min: 0, max: 500000, tickAmount: 5,
                  title: { text: 'Jumlah Penduduk (jiwa)', style: { fontSize: '11px', fontWeight: 500 } },
                  labels: { formatter: v => fmt0(v), style: { fontSize: '10px' } } },
                { seriesName: 'Luas Wilayah', opposite: true, min: 0, max: 100, tickAmount: 5,
                  title: { text: 'Luas Wilayah (km²)', style: { fontSize: '11px', fontWeight: 500 } },
                  labels: { formatter: v => fmt0(v), style: { fontSize: '10px' } } },
                { seriesName: 'Kepadatan Penduduk', opposite: true, min: 0, max: 7000, tickAmount: 7,
                  title: { text: 'Kepadatan (jiwa/km²)', style: { fontSize: '11px', fontWeight: 500 } },
                  labels: { formatter: v => fmt0(v), style: { fontSize: '10px' } } }
            ],
            legend: { position: 'top', fontSize: '12px' },
            grid: { strokeDashArray: 3 },
            tooltip: {
                shared: true, intersect: false,
                y: [
                    { formatter: v => fmt0(v) + ' jiwa' },
                    { formatter: v => fmt1(v) + ' km²' },
                    { formatter: v => fmt0(v) + ' jiwa/km²' }
                ]
            }
        }).render();

        // ---------- Tren kepadatan provinsi ----------
        new ApexCharts(document.querySelector('#chartTren'), {
            chart: { type: 'line', height: 320, toolbar: { show: false } },
            series: [
                { name: 'Aceh (rata-rata)', data: trenAceh },
                { name: 'Kabupaten/Kota (rata-rata)', data: trenKab }
            ],
            colors: ['#4a3fd0', '#60a5fa'],
            stroke: { width: 2.5, curve: 'straight' },
            markers: { size: 4, strokeWidth: 2, strokeColors: '#fff' },
            dataLabels: { enabled: true, formatter: v => fmt1(v), offsetY: -8, style: { fontSize: '10px', fontWeight: 500, colors: ['#475569'] }, background: { enabled: false } },
            xaxis: { categories: tahunTren },
            yaxis: { min: 0, max: 120, tickAmount: 4, labels: { formatter: v => fmt0(v), style: { fontSize: '10px' } } },
            legend: { position: 'bottom', fontSize: '12px', markers: { radius: 3 } },
            grid: { strokeDashArray: 3 },
            tooltip: { y: { formatter: v => fmt1(v) + ' jiwa/km²' } }
        }).render();
    });
</script>
@endsection