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
    .legend-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .45rem 1rem; font-size: .8rem; }
    .legend-item { display: flex; align-items: center; }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; margin-right: .5rem; flex: 0 0 10px; }
    .legend-item .nama { flex: 1; font-weight: 600; }
    .legend-item .nilai { color: #475569; font-variant-numeric: tabular-nums; }
    .chip { display: inline-flex; align-items: center; font-size: .72rem; margin-right: .9rem; }
    .chip i { width: 10px; height: 10px; border-radius: 2px; margin-right: .35rem; display: inline-block; }

    .ternak-map-legend {
        background: rgba(255, 255, 255, .95);
        border: 1px solid #e5e7eb;
        border-radius: .5rem;
        padding: .5rem .65rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
        font-size: .7rem;
    }

    .ternak-map-legend-title {
        font-weight: 700;
        font-size: .68rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: #64748b;
        margin-bottom: .3rem;
    }

    .ternak-map-legend-item {
        display: flex;
        align-items: center;
        gap: .4rem;
        color: #334155;
        line-height: 1.6;
    }

    .ternak-map-swatch {
        display: inline-block;
        width: .65rem;
        height: .65rem;
        border-radius: 2px;
    }

    .ternak-map-tip {
        font-size: .75rem;
    }

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
                <option value="{{ $t }}" @selected((int) $t === (int) $tahunAktif)>{{ $t }}</option>
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
                        <option value="">Semua Ternak</option>
                        @foreach ($jenisTernakList as $j)
                            <option value="{{ $j }}">{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Bagian peta dikosongkan (siapkan untuk Leaflet choropleth) --}}
                <div id="map" data-geojson="{{ asset('js/Penduduk/aceh-kabupaten.geojson') }}"></div>
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
        const populasi        = @json($populasi);
        const totalPopulasi   = {{ $totalPopulasi }};
        const produksiProduk  = @json($produksiPerProduk);
        const labelKombinasi  = @json($labelKombinasi);
        const nilaiKabupaten  = @json($nilaiKabupaten);
        const satuanProduksi  = @json($satuanProduksi);
        const filterTahun = document.getElementById('filterTahun');
        const pageLoading = document.getElementById('pageLoading');
        const loadingText = document.getElementById('loadingText');

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

@push('scripts')
    <script type="application/json" id="data-populasi-ternak">
        @json($petaPopulasi)
    </script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/Ekonomi-Pangan/peternakan-map.js') }}"></script>
@endpush