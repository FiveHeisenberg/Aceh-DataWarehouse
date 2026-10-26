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

    /* ===== Card Per Komoditi (bisa di-scroll) ===== */
    .kpk-card { border: 1px solid #e5e7eb; border-radius: 1.1rem; background: #fff; padding: 1.25rem 1.4rem 1rem; display: flex; flex-direction: column; box-shadow: 0 1px 6px rgba(15, 23, 42, .05); }
    .kpk-title { font-weight: 800; font-size: 1.15rem; color: #0f172a; margin: 0; }
    .kpk-sub { font-size: .8rem; color: #94a3b8; }
    .kpk-select { border: 1px solid #cbd5e1; border-radius: .7rem; font-weight: 700; font-size: .85rem; color: #1e293b; padding: .45rem 2rem .45rem .9rem; min-width: 150px; box-shadow: 0 1px 3px rgba(15, 23, 42, .08); }
    .kpk-divider { border-top: 1px solid #e5e7eb; margin: .9rem 0 .75rem; }

    .kpk-toggles { display: flex; align-items: center; justify-content: flex-end; gap: .5rem; margin-bottom: .6rem; }
    .kpk-toggles .lbl { font-size: .7rem; font-weight: 700; letter-spacing: .04em; color: #94a3b8; text-transform: uppercase; margin-right: .25rem; }
    .kpk-pill { display: inline-flex; align-items: center; gap: .3rem; border: 0; border-radius: 999px; padding: .35rem .85rem; font-size: .8rem; font-weight: 700; color: #fff; transition: opacity .2s, filter .2s; }
    .kpk-pill svg { width: 14px; height: 14px; stroke: #fff; fill: none; stroke-width: 3; stroke-linecap: round; stroke-linejoin: round; }
    .kpk-pill.tanam { background: #9f1f63; }
    .kpk-pill.panen { background: #ea78a8; }
    .kpk-pill.off { opacity: .4; filter: grayscale(.4); }
    .kpk-pill.off svg { visibility: hidden; }

    .kpk-grid { display: grid; grid-template-columns: 130px 1fr; column-gap: .75rem; }
    .kpk-head { align-items: end; font-size: .7rem; font-weight: 700; color: #94a3b8; padding-bottom: .4rem; border-bottom: 1px dashed #e5e7eb; }
    .kpk-ticks { position: relative; height: 16px; margin-right: 22px; }
    .kpk-ticks span { position: absolute; top: 0; transform: translateX(-50%); white-space: nowrap; }
    .kpk-ticks span:first-child { transform: none; }
    .kpk-ticks span:last-child { transform: translateX(-100%); }

    .kpk-body { max-height: 360px; overflow-y: auto; padding: .6rem 6px .4rem 0; margin-right: -4px; scrollbar-width: thin; scrollbar-color: #c3c8d0 transparent; }
    .kpk-body::-webkit-scrollbar { width: 8px; }
    .kpk-body::-webkit-scrollbar-track { background: transparent; }
    .kpk-body::-webkit-scrollbar-thumb { background: #c3c8d0; border-radius: 8px; }

    .kpk-row { margin-bottom: .55rem; align-items: center; }
    .kpk-name { text-align: right; font-size: .95rem; font-weight: 500; color: #475569; line-height: 1.15; padding-right: .3rem; }
    .kpk-bars { background-image: linear-gradient(to right, #eef0f4 1px, transparent 1px); background-size: 25% 100%; background-repeat: repeat-x; }
    .kpk-bar { height: 22px; margin: 2px 0; transition: width .45s ease; min-width: 2px; }
    .kpk-bar.tanam { background: #9f1f63; }
    .kpk-bar.panen { background: #e978a7; }
    .kpk-bar.hidden { display: none; }

    .kpk-foot { display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #e5e7eb; margin-top: .4rem; padding-top: .65rem; font-size: .72rem; color: #64748b; }
    .kpk-foot .ok::before { content: ''; display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #10b981; margin-right: .4rem; }
    .kpk-foot .skala { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; color: #94a3b8; }

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
            {{-- Card Per Komoditi (daftar bar yang bisa di-scroll) --}}
            <div class="kpk-card h-100" id="kpkCard">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <h5 class="kpk-title">Per Komoditi</h5>
                        <div class="kpk-sub">Distribusi luas tanam &amp; panen per wilayah kabupaten</div>
                    </div>
                    <select id="selKomoditas" class="form-select kpk-select w-auto">
                        @foreach ($komoditasList as $k)
                            <option value="{{ $k }}" {{ $k === 'Padi' ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="kpk-divider"></div>

                <div class="kpk-toggles">
                    <span class="lbl">Tampilkan:</span>
                    <button type="button" class="kpk-pill tanam" data-seri="tanam" aria-pressed="true">
                        <svg viewBox="0 0 24 24"><polyline points="4 12 10 18 20 6"/></svg>Luas Tanam
                    </button>
                    <button type="button" class="kpk-pill panen" data-seri="panen" aria-pressed="true">
                        <svg viewBox="0 0 24 24"><polyline points="4 12 10 18 20 6"/></svg>Luas Panen
                    </button>
                </div>

                <div class="kpk-grid kpk-head">
                    <div>Kabupaten / Kota</div>
                    <div class="kpk-ticks" id="kpkTicks"></div>
                </div>

                <div class="kpk-body" id="kpkBody"></div>

                <div class="kpk-foot">
                    <span class="ok">Data Terverifikasi Dinas Pertanian Aceh</span>
                    <span class="skala" id="kpkSkala"></span>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            {{-- Card Per Kabupaten/Kota (daftar bar yang bisa di-scroll) --}}
            <div class="kpk-card h-100" id="kpkCard2">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <h5 class="kpk-title">Per Kabupaten/Kota</h5>
                        <div class="kpk-sub">Distribusi luas tanam &amp; panen per komoditas</div>
                    </div>
                    <select id="selKabupaten" class="form-select kpk-select w-auto">
                        @foreach ($kabupatenList as $k)
                            <option value="{{ $k }}" {{ $k === 'Kota Banda Aceh' ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="kpk-divider"></div>

                <div class="kpk-toggles">
                    <span class="lbl">Tampilkan:</span>
                    <button type="button" class="kpk-pill tanam" data-seri="tanam" aria-pressed="true">
                        <svg viewBox="0 0 24 24"><polyline points="4 12 10 18 20 6"/></svg>Luas Tanam
                    </button>
                    <button type="button" class="kpk-pill panen" data-seri="panen" aria-pressed="true">
                        <svg viewBox="0 0 24 24"><polyline points="4 12 10 18 20 6"/></svg>Luas Panen
                    </button>
                </div>

                <div class="kpk-grid kpk-head">
                    <div>Komoditas</div>
                    <div class="kpk-ticks" id="kpkTicks2"></div>
                </div>

                <div class="kpk-body" id="kpkBody2"></div>

                <div class="kpk-foot">
                    <span class="ok">Data Terverifikasi Dinas Pertanian Aceh</span>
                    <span class="skala" id="kpkSkala2"></span>
                </div>
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

        // ---------- Daftar bar luas tanam & panen (bisa di-scroll) ----------
        // Dipakai oleh card "Per Komoditi" dan "Per Kabupaten/Kota".
        const satuanTampil = String(satuanLuas).charAt(0).toUpperCase() + String(satuanLuas).slice(1);
        const fmtR = n => Math.round(n).toLocaleString('id-ID');

        // Skala "bulat": 4 interval dengan langkah 1 / 2 / 2,5 / 5 x 10^n
        function skalaMaks(nilaiMaks) {
            const mentah  = (nilaiMaks || 1) / 4;
            const pangkat = Math.pow(10, Math.floor(Math.log10(mentah)));
            const langkah = [1, 2, 2.5, 5, 10].map(m => m * pangkat).find(s => s >= mentah);
            return langkah * 4;
        }

        function buatDaftarBar(cfg) {
            const root  = document.getElementById(cfg.root);
            const sel   = document.getElementById(cfg.select);
            const body  = document.getElementById(cfg.body);
            const ticks = document.getElementById(cfg.ticks);
            const skala = document.getElementById(cfg.skala);
            const aktif = { tanam: true, panen: true };

            function render() {
                // cfg.rows(nilaiDropdown) -> [{ nama, tanam, panen }]
                const rows = cfg.rows(sel.value).sort((a, b) => b.tanam - a.tanam);

                // skala mengikuti seri yang sedang ditampilkan
                const nilai = rows.flatMap(r => [aktif.tanam ? r.tanam : 0, aktif.panen ? r.panen : 0]);
                const maks  = skalaMaks(Math.max(0, ...nilai));

                ticks.innerHTML = [0, 1, 2, 3, 4].map(i => {
                    const teks = fmtR(maks / 4 * i) + (i === 4 ? ' ' + satuanTampil : '');
                    return `<span style="left:${i * 25}%">${teks}</span>`;
                }).join('');
                skala.textContent = 'Skala: 0 - ' + fmtR(maks) + ' ' + satuanTampil;

                body.innerHTML = rows.map(r => `
                    <div class="kpk-grid kpk-row">
                        <div class="kpk-name">${r.nama}</div>
                        <div class="kpk-bars">
                            <div class="kpk-bar tanam ${aktif.tanam ? '' : 'hidden'}" style="width:${r.tanam / maks * 100}%"
                                 title="Luas Tanam: ${fmtR(r.tanam)} ${satuanTampil}"></div>
                            <div class="kpk-bar panen ${aktif.panen ? '' : 'hidden'}" style="width:${r.panen / maks * 100}%"
                                 title="Luas Panen: ${fmtR(r.panen)} ${satuanTampil}"></div>
                        </div>
                    </div>`).join('');
                body.scrollTop = 0;
            }

            // tombol "Tampilkan" (minimal satu seri tetap aktif)
            root.querySelectorAll('.kpk-pill').forEach(btn => {
                btn.addEventListener('click', function () {
                    const seri = this.dataset.seri;
                    const lain = seri === 'tanam' ? 'panen' : 'tanam';
                    if (aktif[seri] && !aktif[lain]) return;
                    aktif[seri] = !aktif[seri];
                    this.classList.toggle('off', !aktif[seri]);
                    this.setAttribute('aria-pressed', aktif[seri]);
                    render();
                });
            });

            sel.addEventListener('change', render);
            render();
        }

        // Per Komoditi -> satu baris per kabupaten/kota
        buatDaftarBar({
            root: 'kpkCard', select: 'selKomoditas', ticks: 'kpkTicks', body: 'kpkBody', skala: 'kpkSkala',
            rows: kom => !matrix[kom] ? [] : kabList
                .filter(k => matrix[kom][k])
                .map(k => ({ nama: k, tanam: matrix[kom][k][0], panen: matrix[kom][k][1] }))
        });

        // Per Kabupaten/Kota -> satu baris per komoditas
        buatDaftarBar({
            root: 'kpkCard2', select: 'selKabupaten', ticks: 'kpkTicks2', body: 'kpkBody2', skala: 'kpkSkala2',
            rows: kab => komList
                .filter(k => matrix[k] && matrix[k][kab])
                .map(k => ({ nama: k, tanam: matrix[k][kab][0], panen: matrix[k][kab][1] }))
        });
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