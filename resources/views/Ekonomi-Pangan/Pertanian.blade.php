@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
@php
    $fmt = fn($n) => number_format($n, 0, ',', '.');
@endphp

<style>
    .dw-card { border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; }
    .sec-title { font-weight: 700; font-size: 1.35rem; margin: 1.5rem 0 .75rem; }
    #map { height: 330px; background: #f8fafc; border-radius: .5rem; }
    .legend-row { display: flex; align-items: center; font-size: .85rem; margin-bottom: .6rem; }
    .legend-dot { width: 14px; height: 14px; border-radius: 50%; margin-right: .6rem; flex: 0 0 14px; }
    .prod-row { margin-bottom: 1.1rem; }
    .prod-row .head { display: flex; justify-content: space-between; font-size: .85rem; font-weight: 600; margin-bottom: .25rem; }
    .prod-row .progress { height: 9px; background: #e5eef6; }
    .prod-row .progress-bar { background: #0f9d8a; }
    .stat-box { border: 1px solid #d1d5db; border-radius: .5rem; padding: .5rem .85rem .6rem; margin-bottom: .9rem; }
    .stat-box .lbl { font-size: .72rem; font-weight: 600; color: #111827; }
    .stat-box .val { font-size: 1.9rem; font-weight: 800; line-height: 1.2; color: #0f172a; }
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

    #map {
        height: 330px;
        background: #f8fafc;
        border-radius: .5rem;
    }

    .lahan-map-legend {
        background: rgba(255, 255, 255, .95);
        border: 1px solid #e5e7eb;
        border-radius: .5rem;
        padding: .5rem .65rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
        font-size: .7rem;
    }

    .lahan-map-legend-title {
        font-weight: 700;
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #64748b;
        margin-bottom: .3rem;
    }

    .lahan-map-legend-item {
        display: flex;
        align-items: center;
        gap: .4rem;
        color: #334155;
        line-height: 1.6;
    }

    .lahan-map-swatch {
        display: inline-block;
        width: .65rem;
        height: .65rem;
        border-radius: 2px;
    }

    .lahan-map-tip {
        font-size: .75rem;
    }

</style>

<div class="container-fluid py-3">

    {{-- Breadcrumb + filter tahun --}}
    <div class="d-flex justify-content-between align-items-start mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Ekonomi Pangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pertanian</li>
            </ol>
        </nav>
        <select id="filterTahun" class="form-select form-select-sm w-auto">
            @foreach ($tahunList as $t)
                <option value="{{ $t }}" @selected((int) $t === (int) $tahunAktif)>{{ $t }}</option>
            @endforeach
        </select>
    </div>

    <h2 class="fw-bold mb-3">Persebaran Lahan Pertanian</h2>

    {{-- Peta + komposisi --}}
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="dw-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold mb-0">Luas Lahan Pertanian</h5>
                    <select id="filterJenisLahan" class="form-select form-select-sm w-auto">
                        <option value="">Semua Jenis Lahan</option>
                        @foreach ($jenisLahanList as $j)
                            <option value="{{ $j }}">{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Bagian peta dikosongkan (siapkan untuk Leaflet choropleth) --}}
                <div id="map" data-geojson="{{ asset('js/Penduduk/aceh-kabupaten.geojson') }}"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="dw-card p-3 h-100">
                <h5 class="fw-bold mb-2">Komposisi Luas Lahan</h5>
                <div id="chartKomposisi"></div>
                <div class="mt-2">
                    @foreach ($komposisi as $k)
                        <div class="legend-row">
                            <span class="legend-dot" style="background: {{ $k['warna'] }}"></span>
                            <span class="flex-grow-1">{{ $k['label'] }}</span>
                            <span>{{ $fmt($k['luas']) }} {{ $satuanLuas }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Luas tanam dan panen --}}
    <div class="sec-title">Luas Tanam dan Panen</div>
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold mb-0">Per Komoditi</h5>
                    <select id="selKomoditas" class="form-select form-select-sm w-auto">
                        @foreach ($komoditasList as $k)
                            <option value="{{ $k }}" {{ $k === 'Jagung' ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="chartKomoditas"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold mb-0">Per Kabupaten/Kota</h5>
                    <select id="selKabupaten" class="form-select form-select-sm w-auto">
                        @foreach ($kabupatenList as $k)
                            <option value="{{ $k }}" {{ $k === 'Kota Banda Aceh' ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="chartKabupaten"></div>
            </div>
        </div>
    </div>

    {{-- Sarana produktivitas --}}
    <div class="sec-title">Sarana Produktivitas</div>
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="dw-card p-3 h-100">
                <h5 class="fw-bold mb-3">Produksi Padi Per Wilayah</h5>
                @foreach ($produksiPadi as $p)
                    <div class="prod-row">
                        <div class="head">
                            <span>{{ $p['wilayah'] }}</span>
                            <span>{{ $fmt($p['jumlah']) }} {{ ucfirst($satuanProduksi) }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar"
                                 style="width: {{ round($p['jumlah'] / $maxProduksi * 100, 1) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-4">
            <div class="dw-card p-3 h-100">
                <h5 class="fw-bold mb-3">Jumlah Tenaga Tani Provinsi</h5>
                <div class="stat-box">
                    <div class="lbl">Total Petani</div>
                    <div class="val">{{ $fmt($totalPetani) }}</div>
                </div>
                <div class="stat-box">
                    <div class="lbl">Jumlah Kelompok Tani</div>
                    <div class="val">{{ $fmt($jumlahKelompok) }}</div>
                </div>
                <div class="stat-box mb-0">
                    <div class="lbl">Petani Terbanyak</div>
                    <div class="val">{{ $petaniTerbanyak }}</div>
                </div>
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
        const komposisi = @json($komposisi);
        const matrix    = @json($matrix);
        const kabList   = @json($kabupatenList);
        const komList   = @json($komoditasList);
        const totalLuas = {{ $totalLuas }};
        const satuanLuas = @json($satuanLuas);
        const fmt = n => n.toLocaleString('id-ID');
        const filterTahun = document.getElementById('filterTahun');
        const pageLoading  = document.getElementById('pageLoading');
        const loadingText  = document.getElementById('loadingText');


        // FILTER TAHUN
        filterTahun.addEventListener('change', function () {
            const tahun = this.value;

            filterTahun.disabled = true;
            filterTahun.setAttribute('aria-busy', 'true');
            loadingText.textContent = `Memuat data tahun ${tahun}...`;
            pageLoading.classList.remove('d-none');
            document.body.style.cursor = 'wait';

            const url = new URL(window.location.href);
            url.searchParams.set('tahun', tahun);

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

        // ---------- Donut komposisi luas lahan ----------
        new ApexCharts(document.querySelector('#chartKomposisi'), {
            chart: { type: 'donut', height: 270 },
            series: komposisi.map(d => d.luas),
            labels: komposisi.map(d => d.label),
            colors: komposisi.map(d => d.warna),
            legend: { show: false },
            dataLabels: { enabled: false },
            tooltip: { y: { formatter: v => fmt(v) + ' ' + satuanLuas } },
            plotOptions: {
                pie: { donut: { size: '68%', labels: {
                    show: true,
                    name: { show: false },
                    value: { show: false },
                    total: { show: true, showAlways: true, label: '', formatter: () => fmt(totalLuas) + ' ' + satuanLuas, fontSize: '16px' }
                } } }
            }
        }).render();

        // ---------- Bar luas tanam & panen ----------
        function barOptions(categories, tanam, panen) {
            return {
                chart: { type: 'bar', height: 380, toolbar: { show: false } },
                series: [
                    { name: 'Luas Tanam', data: tanam },
                    { name: 'Luas Panen', data: panen }
                ],
                xaxis: { categories: categories, labels: { formatter: v => fmt(Math.round(v)) } },
                plotOptions: { bar: { horizontal: true, barHeight: '70%', borderRadius: 2 } },
                colors: ['#9f1853', '#e08ab0'],
                dataLabels: { enabled: false },
                legend: { position: 'top', horizontalAlign: 'right', fontSize: '11px' },
                tooltip: { y: { formatter: v => fmt(v) + ' ' + satuanLuas } }
            };
        }

        // Per komoditas -> bar per kabupaten
        function dataPerKomoditas(kom) {
            const rows = kabList.map(k => ({ nama: k, t: matrix[kom][k][0], p: matrix[kom][k][1] }))
                                .sort((a, b) => b.t - a.t);
            return barOptions(rows.map(r => r.nama), rows.map(r => r.t), rows.map(r => r.p));
        }

        // Per kabupaten -> bar per komoditas
        function dataPerKabupaten(kab) {
            const rows = komList.map(k => ({ nama: k, t: matrix[k][kab][0], p: matrix[k][kab][1] }))
                                .sort((a, b) => b.t - a.t);
            return barOptions(rows.map(r => r.nama), rows.map(r => r.t), rows.map(r => r.p));
        }

        const selKom = document.getElementById('selKomoditas');
        const selKab = document.getElementById('selKabupaten');

        const chartKom = new ApexCharts(document.querySelector('#chartKomoditas'), dataPerKomoditas(selKom.value));
        const chartKab = new ApexCharts(document.querySelector('#chartKabupaten'), dataPerKabupaten(selKab.value));
        chartKom.render();
        chartKab.render();

        selKom.addEventListener('change', () => chartKom.updateOptions(dataPerKomoditas(selKom.value)));
        selKab.addEventListener('change', () => chartKab.updateOptions(dataPerKabupaten(selKab.value)));
    });
</script>
@endsection

@push('scripts')
    <script type="application/json" id="data-luas-lahan">
        @json($petaLuasLahan)
    </script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/Ekonomi-Pangan/pertanian-map.js') }}"></script>
@endpush