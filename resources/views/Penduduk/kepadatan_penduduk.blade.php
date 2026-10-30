@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
@php
    $fmt0 = fn($n) => number_format($n, 0, ',', '.');
    $fmt1 = fn($n) => number_format($n, 1, ',', '.');
@endphp

<style>
    .dw-card { border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; }
    .sec-title { font-weight: 800; font-size: 1.5rem; margin: 1.75rem 0 .75rem; }
    #map { height: 320px; background: #f8fafc; border-radius: .5rem; }
    #map:focus,
    .leaflet-container:focus,
    .leaflet-container:focus-visible { outline: none; }

    .map-legend { display: flex; flex-wrap: wrap; gap: .35rem 1rem; padding: .6rem .5rem .15rem; font-size: .7rem; color: #334155; }
    .map-legend-title { width: 100%; font-weight: 700; font-size: .68rem; text-transform: uppercase; letter-spacing: .03em; color: #64748b; }
    .map-legend-item { display: flex; align-items: center; gap: .35rem; }
    .map-swatch { display: inline-block; width: .7rem; height: .7rem; border-radius: 2px; border: 1px solid rgba(0, 0, 0, .12); }

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

    .chart-perbandingan-card {
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
    }

    #chartBanding {
        width: 100%;
    }

    .chart-perbandingan-col {
        min-width: 0;
        max-width: 100%;
    }

    .chart-perbandingan-scroll {
        width: 100%;
        min-width: 0;
        overflow-x: auto;
        overflow-y: hidden;
        contain: inline-size;
        padding-bottom: .5rem;
    }
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
            <option value="">Memuat…</option>
        </select>
    </div>

    {{-- Kartu ringkasan --}}
    <div class="row g-3">
        <div class="col-lg-4">
            <div class="kpi biru">
                <div class="ic"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="lbl">Kepadatan Penduduk Rata-rata Aceh</div>
                    <div><span class="big" id="kpiKepadatan">-</span><span class="unit">jiwa/km²</span></div>
                    <div>
                        <span class="up" id="kpiPerubahan"><i class="bi bi-dash"></i> -</span>
                        <span class="sub ms-1" id="kpiTahunBanding">dibandingkan tahun -</span>
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
                            <div class="wil" id="kpiTertinggiNama">-</div>
                            <div class="wil-val"><span id="kpiTertinggiNilai">-</span> <span class="unit">jiwa/km²</span></div>
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
                            <div class="wil" id="kpiTerendahNama">-</div>
                            <div class="wil-val"><span id="kpiTerendahNilai">-</span> <span class="unit">jiwa/km²</span></div>
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
                <div id="map"></div>
                <div class="map-legend">
                    <div class="map-legend-title">Kepadatan Penduduk (jiwa/km²)</div>
                    <div class="map-legend-item"><span class="map-swatch" style="background:#86efac"></span> Sangat Rendah (&lt; 50)</div>
                    <div class="map-legend-item"><span class="map-swatch" style="background:#fde047"></span> Rendah (50–100)</div>
                    <div class="map-legend-item"><span class="map-swatch" style="background:#fb923c"></span> Sedang (101–250)</div>
                    <div class="map-legend-item"><span class="map-swatch" style="background:#ef4444"></span> Tinggi (251–500)</div>
                    <div class="map-legend-item"><span class="map-swatch" style="background:#991b1b"></span> Sangat Tinggi (&gt; 500)</div>
                </div>
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
    <div class="row g-3">
        <div class="col-12 chart-perbandingan-col">
            <div class="chart-wrap chart-perbandingan-card">
                <div class="chart-perbandingan-scroll">
                    <div id="chartBanding"></div>
                </div>
            </div>
        </div>
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const API_URL = '/api/penduduk/kepadatan-penduduk';
        const GEOJSON_URL = @json(asset('js/Penduduk/aceh-kabupaten.geojson'));

        const fmt0 = n => Number(n).toLocaleString('id-ID');
        const fmt1 = n => Number(n).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 });

        const warnaBar = ['#2b44c9', '#2f52d6', '#3361e0', '#3a72e8', '#4281ee', '#4d90f2', '#5a9ef5', '#69aaf6', '#7bb6f7', '#8ec3f8'];
        const rankList   = document.getElementById('rankList');
        const rankSub    = document.getElementById('rankSub');
        const selectTahun = document.getElementById('filterTahun');

        let peringkat = [];
        let chartBanding = null;
        let chartTren = null;
        let petaMap = null;
        let petaGeoLayer = null;
        let petaData = new Map();
        const petaLayers = new Map();

        // ---------- Peringkat kepadatan (Tertinggi / Terendah) ----------
        function renderRank(mode) {
            if (!peringkat.length) {
                rankSub.textContent = '';
                rankList.innerHTML = '';
                return;
            }

            const rows = mode === 'tertinggi'
                ? peringkat.slice(0, 10)
                : peringkat.slice(-10).reverse();
            const maks = Math.max(...rows.map(r => r.kepadatan), 1);

            rankSub.textContent = '10 Kabupaten/Kota dengan Kepadatan ' + (mode === 'tertinggi' ? 'Tertinggi' : 'Terendah');
            rankList.innerHTML = rows.map((r, i) => `
                <div class="rank-row">
                    <span class="no">${i + 1}.</span>
                    <span class="nama" title="${r.nama}">${r.nama}</span>
                    <div class="track"><div class="fill" style="width:${(r.kepadatan / maks * 100).toFixed(1)}%;background:${warnaBar[i]}"></div></div>
                    <span class="val">${fmt0(r.kepadatan)}</span>
                </div>`).join('');
        }

        document.querySelectorAll('#toggleRank .btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#toggleRank .btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                renderRank(this.dataset.mode);
            });
        });

        // ---------- KPI ----------
        function renderKpi(data) {
            const r = data.ringkasan;

            document.getElementById('kpiKepadatan').textContent = fmt1(r.kepadatan);

            const elPerubahan = document.getElementById('kpiPerubahan');
            if (r.perubahan_persen === null || r.perubahan_persen === undefined) {
                elPerubahan.innerHTML = '<i class="bi bi-dash"></i> -';
                elPerubahan.style.color = '#94a3b8';
            } else {
                const naik = r.perubahan_persen >= 0;
                elPerubahan.innerHTML = `<i class="bi bi-arrow-${naik ? 'up' : 'down'}"></i> ${fmt1(Math.abs(r.perubahan_persen))}%`;
                elPerubahan.style.color = naik ? '#16a34a' : '#dc2626';
            }

            document.getElementById('kpiTahunBanding').textContent = r.tahun_pembanding
                ? `dibandingkan tahun ${r.tahun_pembanding}`
                : 'belum ada data pembanding';

            document.getElementById('kpiTertinggiNama').textContent = r.tertinggi ? r.tertinggi.nama : '-';
            document.getElementById('kpiTertinggiNilai').textContent = r.tertinggi ? fmt0(r.tertinggi.kepadatan) : '-';
            document.getElementById('kpiTerendahNama').textContent = r.terendah ? r.terendah.nama : '-';
            document.getElementById('kpiTerendahNilai').textContent = r.terendah ? fmt0(r.terendah.kepadatan) : '-';
        }

        // ---------- Perbandingan penduduk, luas wilayah, kepadatan ----------
        function renderBanding(rows) {
            const elChartBanding = document.querySelector('#chartBanding');
            const lebarChartBanding = Math.max(850, rows.length * 160);
            elChartBanding.style.width = lebarChartBanding + 'px';

            if (chartBanding) {
                chartBanding.destroy();
                chartBanding = null;
            }

            const batas = values => Math.ceil(Math.max(...values, 1) * 1.2);

            chartBanding = new ApexCharts(elChartBanding, {
                chart: { type: 'line', height: 380, width: lebarChartBanding, toolbar: { show: false }, zoom: { enabled: false } },
                series: [
                    { name: 'Jumlah Penduduk', type: 'column', data: rows.map(b => b.penduduk) },
                    { name: 'Luas Wilayah',    type: 'column', data: rows.map(b => b.luas) },
                    { name: 'Kepadatan Penduduk', type: 'line', data: rows.map(b => b.kepadatan) }
                ],
                colors: ['#2f6fe4', '#f59e42', '#5b4fe0'],
                stroke: { width: [0, 0, 2.5], curve: 'smooth' },
                markers: { size: [0, 0, 4], strokeWidth: 2, strokeColors: '#fff' },
                plotOptions: { bar: { columnWidth: '55%', borderRadius: 2 } },
                dataLabels: { enabled: false },
                xaxis: { categories: rows.map(b => b.nama), labels: { style: { fontSize: '11px' } } },
                yaxis: [
                    { seriesName: 'Jumlah Penduduk', min: 0, max: batas(rows.map(b => b.penduduk)), tickAmount: 5,
                      title: { text: 'Jumlah Penduduk (jiwa)', style: { fontSize: '11px', fontWeight: 500 } },
                      labels: { formatter: v => fmt0(v), style: { fontSize: '10px' } } },
                    { seriesName: 'Luas Wilayah', opposite: true, min: 0, max: batas(rows.map(b => b.luas)), tickAmount: 5,
                      title: { text: 'Luas Wilayah (km²)', style: { fontSize: '11px', fontWeight: 500 } },
                      labels: { formatter: v => fmt0(v), style: { fontSize: '10px' } } },
                    { seriesName: 'Kepadatan Penduduk', opposite: true, min: 0, max: batas(rows.map(b => b.kepadatan)), tickAmount: 5,
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
                        { formatter: v => fmt1(v) + ' jiwa/km²' }
                    ]
                }
            });
            chartBanding.render();
        }

        // ---------- Tren kepadatan provinsi ----------
        function renderTren(rows) {
            const elChartTren = document.querySelector('#chartTren');
            if (chartTren) {
                chartTren.destroy();
                chartTren = null;
            }

            const maks = Math.max(...rows.map(t => Math.max(t.kepadatan_provinsi, t.rata_kabupaten)), 1);

            chartTren = new ApexCharts(elChartTren, {
                chart: { type: 'line', height: 320, toolbar: { show: false }, zoom: { enabled: false } },
                series: [
                    { name: 'Aceh (rata-rata)', data: rows.map(t => t.kepadatan_provinsi) },
                    { name: 'Kabupaten/Kota (rata-rata)', data: rows.map(t => t.rata_kabupaten) }
                ],
                colors: ['#4a3fd0', '#60a5fa'],
                stroke: { width: 2.5, curve: 'straight' },
                markers: { size: 4, strokeWidth: 2, strokeColors: '#fff' },
                dataLabels: { enabled: true, formatter: v => fmt1(v), offsetY: -8, style: { fontSize: '10px', fontWeight: 500, colors: ['#475569'] }, background: { enabled: false } },
                xaxis: { categories: rows.map(t => t.tahun) },
                yaxis: { min: 0, max: Math.ceil(maks * 1.2), tickAmount: 4, labels: { formatter: v => fmt0(v), style: { fontSize: '10px' } } },
                legend: { position: 'bottom', fontSize: '12px', markers: { radius: 3 } },
                grid: { strokeDashArray: 3 },
                tooltip: { y: { formatter: v => fmt1(v) + ' jiwa/km²' } }
            });
            chartTren.render();
        }

        // ---------- Peta sebaran tingkat kepadatan ----------
        const normalisasiNama = nama => String(nama || '')
            .replace(/^(Kabupaten|Kota)\s+/i, '')
            .trim()
            .toLowerCase();

        const kategoriKepadatan = nilai => {
            if (nilai === null || nilai === undefined || Number.isNaN(nilai)) {
                return { label: 'Belum ada data', color: '#e2e8f0' };
            }
            if (nilai > 500) return { label: 'Sangat Tinggi', color: '#991b1b' };
            if (nilai > 250) return { label: 'Tinggi', color: '#ef4444' };
            if (nilai > 100) return { label: 'Sedang', color: '#fb923c' };
            if (nilai >= 50) return { label: 'Rendah', color: '#fde047' };

            return { label: 'Sangat Rendah', color: '#86efac' };
        };

        function stylePeta(feature) {
            const key = normalisasiNama(feature.properties?.nama);
            const row = petaData.get(key);
            const kategori = kategoriKepadatan(row ? Number(row.kepadatan) : null);

            return {
                color: '#ffffff',
                weight: 1.5,
                fillColor: kategori.color,
                fillOpacity: row ? 0.85 : 0.35
            };
        }

        function tooltipPeta(nama, row) {
            if (!row) {
                return `<strong>${nama}</strong><br>Data kepadatan belum tersedia`;
            }

            const kategori = kategoriKepadatan(Number(row.kepadatan));

            return `<strong>${row.nama}</strong><br>
                Kepadatan: ${fmt1(row.kepadatan)} jiwa/km²<br>
                Kategori: ${kategori.label}<br>
                Penduduk: ${fmt0(row.penduduk)} jiwa<br>
                Luas: ${fmt1(row.luas)} km²`;
        }

        async function renderPeta(rows) {
            if (typeof L === 'undefined') {
                return;
            }

            const mapElement = document.getElementById('map');
            if (!mapElement) {
                return;
            }

            petaData = new Map(rows.map(row => [normalisasiNama(row.nama), row]));

            if (petaGeoLayer) {
                petaGeoLayer.setStyle(stylePeta);
                petaLayers.forEach((layer, key) => {
                    const nama = layer.feature?.properties?.nama || key;
                    layer.setTooltipContent(tooltipPeta(nama, petaData.get(key)));
                });
                return;
            }

            petaMap = L.map(mapElement, { scrollWheelZoom: false });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 18
            }).addTo(petaMap);

            try {
                const geojson = await fetch(GEOJSON_URL).then(response => {
                    if (!response.ok) {
                        throw new Error('GeoJSON gagal dimuat');
                    }

                    return response.json();
                });

                petaGeoLayer = L.geoJSON(geojson, {
                    style: stylePeta,
                    onEachFeature: function (feature, layer) {
                        const key = normalisasiNama(feature.properties?.nama);
                        const nama = feature.properties?.nama || '-';

                        petaLayers.set(key, layer);
                        layer.bindTooltip(tooltipPeta(nama, petaData.get(key)), { sticky: true });

                        layer.on({
                            mouseover: function (event) {
                                event.target.setStyle({ weight: 3, color: '#0f172a', fillOpacity: 0.95 });
                                event.target.bringToFront();
                            },
                            mouseout: function (event) {
                                petaGeoLayer.resetStyle(event.target);
                            }
                        });
                    }
                }).addTo(petaMap);

                petaMap.fitBounds(petaGeoLayer.getBounds(), { padding: [15, 15] });
            } catch (err) {
                console.error(err);
                mapElement.innerHTML = '<div class="text-danger small p-2">Peta gagal dimuat.</div>';
            }
        }

        // ---------- Load data dari API ----------
        async function loadData(tahun) {
            try {
                const url = new URL(API_URL, window.location.origin);
                if (tahun) {
                    url.searchParams.set('tahun', tahun);
                }

                const res = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!res.ok) {
                    throw new Error('Gagal memuat data (HTTP ' + res.status + ')');
                }

                const json = await res.json();
                if (!json.success) {
                    throw new Error(json.message || 'Gagal memuat data kepadatan penduduk');
                }

                const data = json.data;

                if (selectTahun.dataset.loaded !== '1') {
                    selectTahun.innerHTML = data.tahun_tersedia
                        .map(t => `<option value="${t}">${t}</option>`)
                        .join('');
                    selectTahun.dataset.loaded = '1';
                }
                selectTahun.value = data.tahun;

                peringkat = data.peringkat;
                renderKpi(data);
                renderRank(document.querySelector('#toggleRank .btn.active')?.dataset.mode || 'tertinggi');
                renderBanding(data.banding);
                renderTren(data.tren);
                renderPeta(data.banding);
            } catch (err) {
                console.error(err);
                rankSub.textContent = '';
                rankList.innerHTML = `<div class="text-danger small">${err.message}</div>`;
            }
        }

        selectTahun.addEventListener('change', function () {
            loadData(this.value);
        });

        loadData();
    });
</script>
@endsection