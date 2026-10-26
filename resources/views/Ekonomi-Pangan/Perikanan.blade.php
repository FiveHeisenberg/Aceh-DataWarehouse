@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
@php
    $fmt = fn($n) => number_format($n, 0, ',', '.');
@endphp

<style>
    .dw-card { border-radius: .5rem; background: #fff; }
    .sec-title { font-weight: 700; font-size: 1.6rem; margin: 0; }
    #map { height: 300px; background: #f8fafc; border-radius: .4rem; }
    .fish-card { background: #fff; border: 1px solid #e5e7eb; border-bottom: 3px solid #1e5fa8; border-radius: .4rem; padding: .55rem .8rem; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
    .fish-card .nama { font-size: .7rem; font-weight: 600; color: #334155; text-transform: uppercase; display: flex; align-items: center; gap: .4rem; }
    .fish-card .nilai { font-size: .95rem; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: .4rem; }
    .fish-card svg { width: 22px; height: 12px; flex: 0 0 22px; fill: #1e5fa8; }

    .perikanan-map-legend {
        background: rgba(255, 255, 255, .95);
        border: 1px solid #e5e7eb;
        border-radius: .4rem;
        padding: .45rem .6rem;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .12);
        line-height: 1.5;
    }

    .perikanan-map-legend-title {
        font-weight: 700;
        font-size: .68rem;
        color: #0f172a;
        margin-bottom: .3rem;
    }

    .perikanan-map-legend-item {
        display: flex;
        align-items: center;
        font-size: .66rem;
        color: #334155;
        white-space: nowrap;
    }

    .perikanan-map-swatch {
        display: inline-block;
        width: .65rem;
        height: .65rem;
        border-radius: 2px;
        margin-right: .4rem;
    }

    .perikanan-map-legend-empty {
        font-size: .68rem;
        color: #64748b;
    }

    .perikanan-map-tip {
        font-size: .75rem;
    }

</style>

<div class="container-fluid py-3">

    {{-- Breadcrumb + filter jenis perikanan + tahun --}}
    <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Ekonomi Pangan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Perikanan</li>
            </ol>
        </nav>
        <div class="d-flex gap-2">
            <select id="selJenisPerikanan" class="form-select form-select-sm w-auto">
                @foreach ($jenisPerikananList as $key => $label)
                    <option value="{{ $key }}" @selected($key === $jenisAwal)>{{ $label }}</option>
                @endforeach
            </select>
            <select id="selTahun" class="form-select form-select-sm w-auto">
                @foreach ($tahunList as $t)
                    <option value="{{ $t }}" @selected($key === $jenisAwal)>{{ $t }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <h2 class="fw-bold mb-3">Persebaran Produksi Perikanan</h2>

    {{-- Peta + perbandingan produksi --}}
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="dw-card p-3 h-100">
                <h5 class="fw-bold mb-2">Peta Produksi</h5>
                {{-- Bagian peta dikosongkan (siapkan untuk Leaflet choropleth) --}}
                <div id="map" data-geojson="{{ asset('js/Penduduk/aceh-kabupaten.geojson') }}"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="dw-card p-3 h-100">
                <h5 class="fw-bold mb-2">Perbandingan Produksi (Ton)</h5>
                <div id="chartWilayah"></div>
            </div>
        </div>
    </div>

    {{-- Produksi per tahun --}}
    <div class="mt-4">
        <h3 class="sec-title">Produksi Perikanan</h3>
        <div class="small text-muted mb-2">Berdasarkan Tahun (Ton) &middot; <span id="lblJenisTahun"></span></div>
        <div class="dw-card p-3" style="border-width:1px; border-color:#e5e7eb;">
            <div id="chartTahun"></div>
        </div>
    </div>

    {{-- Produksi per jenis ikan --}}
    <div class="mt-4 mb-4">
        <h3 class="sec-title mb-3">Produksi Perikanan Per Jenis <span class="fs-6 fw-normal text-muted">(<span id="lblTahunJenis"></span>)</span></h3>
        <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-lg-4" id="cardsJenis"></div>
    </div>
</div>

{{-- ApexCharts (hapus jika layouts.app sudah memuatnya) --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const perTahun   = @json($perTahun);
        const perJenis   = @json($perJenis);
        const perWilayah = @json($perWilayah);
        const labelJenis = @json($jenisPerikananList);

        const $    = id => document.getElementById(id);
        const fmt0 = n => Number(n).toLocaleString('id-ID');

        const fishSvg = '<svg viewBox="0 0 24 12"><path d="M0 6 C4 0 12 0 17 6 C12 12 4 12 0 6 Z"/><path d="M16 6 L24 0 L24 12 Z"/></svg>';

        const jenisTerpilih = () => $('selJenisPerikanan').value;
        const tahunTerpilih = () => $('selTahun').value;

        const labelWilayah = nama =>
            nama.replace(/^Kabupaten /, 'Kab. ').toUpperCase();

        // ---------- Perbandingan produksi per kab/kota ----------
        function optWilayah() {
            const jenis = jenisTerpilih();
            const tahun = tahunTerpilih();

            const rows = (perWilayah[jenis]?.[tahun] || [])
                .slice()
                .sort((a, b) => b.volume - a.volume)
                .slice(0, 10);

            return {
                series: [{
                    name: 'Produksi',
                    data: rows.map(r => Math.round(r.volume))
                }],
                noData: {
                    text: `Data produksi belum tersedia untuk tahun ${tahun}`,
                    style: { fontSize: '12px', color: '#64748b' }
                },
                xaxis: {
                    categories: rows.map(r => labelWilayah(r.wilayah)),
                    labels: { show: false }
                }
            };
        }

        const chartWilayah = new ApexCharts($('chartWilayah'), Object.assign({
            chart: { type: 'bar', height: 330, toolbar: { show: false } },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '70%',
                    borderRadius: 2
                }
            },
            colors: ['#9cd94a'],
            dataLabels: {
                enabled: true,
                formatter: v => fmt0(v),
                textAnchor: 'start',
                offsetX: 4,
                style: { fontSize: '10px', colors: ['#0f172a'] },
                background: { enabled: false }
            },
            yaxis: {
                labels: { style: { fontSize: '9px' }, maxWidth: 110 }
            },
            grid: { show: false },
            tooltip: { y: { formatter: v => fmt0(v) + ' ton' } }
        }, optWilayah()));

        chartWilayah.render();

        // ---------- Produksi per tahun ----------
        function optTahun() {
            const jenis = jenisTerpilih();
            const tahunDipilih = Number(tahunTerpilih());
            const tahun = Object.keys(perTahun[jenis] || {})
                .map(Number)
                .sort((a, b) => a - b);

            $('lblJenisTahun').textContent = labelJenis[jenis];

            return {
                series: [{
                    name: labelJenis[jenis],
                    data: tahun.map(t => Math.round(perTahun[jenis][t] || 0))
                }],
                noData: {
                    text: 'Data produksi tahunan belum tersedia',
                    style: { fontSize: '12px', color: '#64748b' }
                },
                xaxis: { categories: tahun },
                colors: tahun.map(t =>
                    t === tahunDipilih ? '#1e88c7' : '#40b6ec'
                )
            };
        }

        const chartTahun = new ApexCharts($('chartTahun'), Object.assign({
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            plotOptions: {
                bar: {
                    distributed: true,
                    columnWidth: '60%',
                    borderRadius: 2,
                    dataLabels: { position: 'top' }
                }
            },
            legend: { show: false },
            dataLabels: {
                enabled: true,
                formatter: v => fmt0(v),
                offsetY: -22,
                style: { fontSize: '12px', colors: ['#64748b'] }
            },
            yaxis: { labels: { formatter: v => fmt0(Math.round(v)) } },
            grid: { strokeDashArray: 3 },
            tooltip: { y: { formatter: v => fmt0(v) + ' ton' } }
        }, optTahun()));

        chartTahun.render();

        // ---------- Kartu produksi per jenis ikan ----------
        function renderKartu() {
            const jenis = jenisTerpilih();
            const tahun = tahunTerpilih();

            const rows = (perJenis[jenis]?.[tahun] || [])
                .slice()
                .sort((a, b) => b.volume - a.volume)
                .slice(0, 10);

            $('lblTahunJenis').textContent = tahun;

            if (!rows.length) {
                $('cardsJenis').innerHTML =
                    '<div class="col"><div class="text-muted small">Tidak ada data produksi untuk tahun ini.</div></div>';
                return;
            }

            $('cardsJenis').innerHTML = rows.map((r, i) => `
                <div class="col">
                    <div class="fish-card h-100">
                        <div class="nama">
                            ${fishSvg}<span>${i + 1}. ${r.nama}</span>
                        </div>
                        <div class="nilai">
                            ${fishSvg}<span>${fmt0(Math.round(r.volume))} ton</span>
                        </div>
                    </div>
                </div>`).join('');
        }

        renderKartu();

        // ---------- Dropdown berlaku untuk semua grafik ----------
        function refreshAll() {
            chartWilayah.updateOptions(optWilayah());
            chartTahun.updateOptions(optTahun());
            renderKartu();
            // TODO: update layer peta Leaflet sesuai jenis/tahun terpilih
        }

        $('selJenisPerikanan').addEventListener('change', refreshAll);
        $('selTahun').addEventListener('change', refreshAll);
    });
</script>
@endsection

@push('scripts')
    <script type="application/json" id="data-produksi-perikanan">
        @json($perWilayah)
    </script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/Ekonomi-Pangan/perikanan-map.js') }}"></script>
@endpush