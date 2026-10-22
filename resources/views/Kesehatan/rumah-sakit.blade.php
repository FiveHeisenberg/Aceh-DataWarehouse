@extends('layouts.app')

@push('styles')
    <meta name="geojson-url" content="{{ asset('aceh-regencies.json') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        #map { height: 420px; background: #f8fafc; border-radius: .5rem; }
        #map .leaflet-interactive:focus,
        #map path.leaflet-interactive:focus {
            outline: none;
        }
        /* Leaflet membuat container peta focusable (container.tabIndex = '0'),
           sehingga browser menggambar focus ring hitam saat area peta diklik. */
        #map:focus,
        .leaflet-container:focus,
        .leaflet-container:focus-visible {
            outline: none;
        }
        .map-tip { font-size: .75rem; }
        .rs-map-legend {
            background: rgba(255, 255, 255, .94); border: 1px solid #e5e7eb;
            border-radius: .5rem; padding: .5rem .65rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1); font-size: .7rem;
        }
        .rs-map-legend-title {
            font-weight: 700; font-size: .68rem; text-transform: uppercase;
            letter-spacing: .03em; color: #64748b; margin-bottom: .3rem;
        }
        .rs-map-legend-item { display: flex; align-items: center; gap: .4rem; color: #334155; line-height: 1.6; }
        .rs-map-legend-item b { margin-left: auto; color: #0f172a; }
        .rs-dot {
            display: inline-block; width: .6rem; height: .6rem; border-radius: 50%;
            border: 1px solid rgba(0, 0, 0, .15);
        }
        .rs-popup { font-size: .78rem; }
        .rs-popup-title { font-weight: 700; color: #0f172a; margin-bottom: .35rem; }
        .rs-popup table { border-collapse: collapse; width: 100%; }
        .rs-popup th {
            text-align: left; color: #64748b; font-weight: 600;
            padding: .12rem .5rem .12rem 0; white-space: nowrap; vertical-align: top;
        }
        .rs-popup td { padding: .12rem 0; color: #0f172a; }
    </style>
@endpush

@section('content')

<style>
    .dw-card { border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; }
    .dw-stat { border: 1px solid #e5e7eb; border-radius: .75rem; padding: .85rem 1rem; }
    .dw-stat .label { font-size: .65rem; font-weight: 700; letter-spacing: .03em; color: #64748b; text-transform: uppercase; }
    .dw-stat .value { font-size: 1.6rem; font-weight: 700; color: #0f172a; line-height: 1.2; }
    .dw-stat .desc { font-size: .75rem; color: #64748b; }
    .dw-stat.s1 { background: #f0f7ff; }
    .dw-stat.s2 { background: #f0fdf9; }
    .dw-stat.s3 { background: #fff8ee; }
    .dw-chart-panel { background: #f1f5f9; border-radius: .75rem; padding: .75rem; }
    .dw-chart-inner { background: #fff; border: 1px solid #e5e7eb; border-radius: .6rem; padding: .5rem; }
    .table thead th { font-size: .85rem; white-space: nowrap; }
    .table tbody td { font-size: .85rem; vertical-align: middle; padding-top: .9rem; padding-bottom: .9rem; }
</style>

<div class="container-fluid py-3">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-3">
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Kesehatan</a></li>
            <li class="breadcrumb-item active" aria-current="page">Rumah Sakit</li>
        </ol>
    </nav>

    {{-- Kartu statistik --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="dw-stat s1">
                <div class="label">Total Rumah Sakit</div>
                <div class="value">{{ $totalRs }}</div>
                <div class="desc">RS aktif, selain RS Darurat</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dw-stat s2">
                <div class="label">Pemilik Terbanyak</div>
                <div class="value">{{ $pemilik[0]['jumlah'] }}</div>
                <div class="desc">{{ $pemilik[0]['label'] }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dw-stat s3">
                <div class="label">Jenis Terbanyak</div>
                <div class="value">{{ $jenis[0]['jumlah'] }}</div>
                <div class="desc">{{ $jenis[0]['label'] }}</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="dw-chart-panel">
                <div class="d-flex justify-content-between small fw-bold mb-1">
                    <span>Filter kepemilikan</span>
                    <a href="#" class="small text-decoration-none btn-reset" data-target="filterPemilik">Reset</a>
                </div>
                <select id="filterPemilik" class="form-select form-select-sm">
                    <option value="">Pilih kepemilikan</option>
                    @foreach ($pemilik as $p)
                        <option value="{{ $p['label'] }}">{{ $p['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dw-chart-panel">
                <div class="d-flex justify-content-between small fw-bold mb-1">
                    <span>Filter kelas</span>
                    <a href="#" class="small text-decoration-none btn-reset" data-target="filterKelas">Reset</a>
                </div>
                <select id="filterKelas" class="form-select form-select-sm">
                    <option value="">Pilih kelas</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k['label'] }}">{{ $k['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dw-chart-panel">
                <div class="d-flex justify-content-between small fw-bold mb-1">
                    <span>Filter jenis</span>
                    <a href="#" class="small text-decoration-none btn-reset" data-target="filterJenis">Reset</a>
                </div>
                <select id="filterJenis" class="form-select form-select-sm">
                    <option value="">Pilih jenis</option>
                    @foreach ($jenis as $j)
                        <option value="{{ $j['label'] }}">{{ $j['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Peta + Donut kelas --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="dw-card p-3 h-100">
                <h5 class="fw-bold mb-3">Peta Persebaran Rumah Sakit</h5>
                {{-- Bagian peta dikosongkan (siapkan untuk Leaflet) --}}
                <div id="map"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="dw-card p-3 h-100">
                <h6 class="text-center fw-semibold mb-2">Rekapulasi RS Per Kelas</h6>
                <div id="chartKelas"></div>
            </div>
        </div>
    </div>

    {{-- Rekap per pemilik & per jenis --}}
    <div class="dw-chart-panel mb-3">
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="dw-chart-inner h-100 p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-semibold small">Rekap RS Per Pemilik (Propinsi:ACEH)</div>
                            <div class="text-muted" style="font-size:.7rem">Klik batang untuk filter daftar RS (bisa pilih beberapa kepemilikan).</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill text-primary bg-primary-subtle">{{ $totalRs }} RS</span>
                            <div class="btn-group btn-group-sm" role="group" id="toggle-pemilik">
                                <button type="button" class="btn btn-outline-secondary active" data-type="bar">Bar</button>
                                <button type="button" class="btn btn-outline-secondary" data-type="column">Kolom</button>
                                <button type="button" class="btn btn-outline-secondary" data-type="donut">Donut</button>
                            </div>
                        </div>
                    </div>
                    <div id="chartPemilik"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dw-chart-inner h-100 p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-semibold small">Rekap RS Per Jenis (Propinsi:ACEH)</div>
                            <div class="text-muted" style="font-size:.7rem">Klik kategori untuk filter daftar RS (bisa pilih beberapa jenis).</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill text-primary bg-primary-subtle">{{ $totalRs }} RS</span>
                            <div class="btn-group btn-group-sm" role="group" id="toggle-jenis">
                                <button type="button" class="btn btn-outline-secondary active" data-type="column">Kolom</button>
                                <button type="button" class="btn btn-outline-secondary" data-type="bar">Bar</button>
                                <button type="button" class="btn btn-outline-secondary" data-type="donut">Donut</button>
                            </div>
                        </div>
                    </div>
                    <div id="chartJenis"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel list rumah sakit --}}
    <div class="dw-card p-3">
        <h3 class="text-center fw-normal mb-0">List Rumah Sakit</h3>
        <div class="text-center text-muted small mb-3"><span id="jumlahTampil">{{ count($daftarRs) }}</span> RS</div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelRs">
                <thead>
                    <tr>
                        <th>Rumah Sakit</th>
                        <th>Kab/Kota</th>
                        <th>Alamat</th>
                        <th>Telp</th>
                        <th>Pemilik</th>
                        <th>Kelas</th>
                        <th>Jenis</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daftarRs as $rs)
                        <tr data-pemilik="{{ $rs['pemilik'] }}" data-kelas="{{ $rs['kelas'] }}" data-jenis="{{ $rs['jenis'] }}">
                            <td>{{ $rs['nama'] }}</td>
                            <td>{{ $rs['kota'] }}</td>
                            <td>{{ $rs['alamat'] }}</td>
                            <td>{{ $rs['telp'] }}</td>
                            <td>{{ $rs['pemilik'] }}</td>
                            <td>{{ $rs['kelas'] }}</td>
                            <td>{{ $rs['jenis'] }}</td>
                            <td><Button>Profile</Button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ApexCharts (hapus baris ini jika layouts.app sudah memuatnya) --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const palette = ['#3b82f6', '#14b8a6', '#f59e0b', '#8b5cf6', '#ef4444', '#06b6d4'];

        const dataKelas   = @json($kelas);
        const dataPemilik = @json($pemilik);
        const dataJenis   = @json($jenis);
        const semuaRs     = @json($daftarRs);
        const total       = {{ $totalRs }};
        const stateFilter = { kelas: '', pemilik: '', jenis: '' };

        function cocokDenganFilter(rs) {
            return (!stateFilter.kelas || rs.kelas === stateFilter.kelas)
                && (!stateFilter.pemilik || rs.pemilik === stateFilter.pemilik)
                && (!stateFilter.jenis || rs.jenis === stateFilter.jenis);
        }

        function rekap(data, field) {
            const hasil = new Map();

            data.filter(cocokDenganFilter).forEach(rs => {
                const label = rs[field] || '-';
                hasil.set(label, (hasil.get(label) || 0) + 1);
            });

            return Array.from(hasil, ([label, jumlah]) => ({ label, jumlah }))
                .sort((a, b) => {
                    if (b.jumlah !== a.jumlah) {
                        return b.jumlah - a.jumlah;
                    }

                    return a.label.localeCompare(b.label, 'id');
                });
        }

        function warnaKelas(label) {
            const item = dataKelas.find(kelas => kelas.label === label);
            return item ? item.warna : '#64748b';
        }

        function buildOptions(type, data, selectId) {
            const labels = data.map(d => d.label);
            const values = data.map(d => d.jumlah);
            const colors = selectId === 'filterKelas' ? data.map(d => warnaKelas(d.label)) : palette;
            const events = {
                dataPointSelection: function (e, chart, cfg) {
                    const label = data[cfg.dataPointIndex]?.label;
                    if (!label) return;
                    const select = document.getElementById(selectId);
                    select.value = select.value === label ? '' : label;
                    applyFilter();
                }
            };

            if (type === 'donut') {
                return {
                    chart: {
                        type: 'donut',
                        height: 360,
                        events
                    },
                    series: values.length ? values : [0],
                    labels: labels.length ? labels : ['Tidak ada data'],
                    colors: colors.length ? colors : ['#cbd5e1'],
                    legend: {
                        position: 'bottom',
                        fontSize: '11px'
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: (value, options) => {
                            return options.w.config.series[options.seriesIndex];
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '62%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total RS',
                                        formatter: () => values.reduce((sum, value) => sum + value, 0)
                                    }
                                }
                            }
                        }
                    },
                    noData: {
                        text: 'Tidak ada data'
                    }
                };
            }

            return {
                chart: { type: 'bar', height: selectId === 'filterKelas' ? 300 : 360, toolbar: { show: true }, events },
                series: [{ name: 'Jumlah RS', data: values }],
                xaxis: { categories: labels, title: { text: type === 'bar' ? 'Jumlah RS' : undefined } },
                yaxis: { title: { text: type === 'column' ? 'Jumlah RS' : undefined } },
                plotOptions: { bar: { horizontal: type === 'bar', distributed: true, borderRadius: 4, columnWidth: '55%' } },
                colors,
                legend: { show: false },
                dataLabels: { enabled: true, style: { colors: ['#0f172a'] }, offsetX: type === 'bar' ? 20 : 0 }
            };
        }

        function makeCharts(selector, data, initialType, selectId) {
            let currentData = data;
            let currentType = initialType;
            let chart = new ApexCharts(document.querySelector(selector), buildOptions(currentType, currentData, selectId));
            chart.render();
            return {
                switchType(type) {
                    currentType = type;
                    chart.destroy();
                    chart = new ApexCharts(document.querySelector(selector), buildOptions(currentType, currentData, selectId));
                    chart.render();
                },
                updateData(nextData) {
                    currentData = nextData;
                    chart.destroy();
                    chart = new ApexCharts(document.querySelector(selector), buildOptions(currentType, currentData, selectId));
                    chart.render();
                }
            };
        }

        const chartKelas   = makeCharts('#chartKelas',   dataKelas,   'donut',  'filterKelas');
        const chartPemilik = makeCharts('#chartPemilik', dataPemilik, 'bar',    'filterPemilik');
        const chartJenis   = makeCharts('#chartJenis',   dataJenis,   'column', 'filterJenis');

        function updateAllCharts() {
            chartKelas.updateData(rekap(semuaRs, 'kelas'));
            chartPemilik.updateData(rekap(semuaRs, 'pemilik'));
            chartJenis.updateData(rekap(semuaRs, 'jenis'));
        }

        function bindToggle(groupId, chartObj) {
            document.querySelectorAll('#' + groupId + ' button').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('#' + groupId + ' button').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    chartObj.switchType(this.dataset.type);
                });
            });
        }
        bindToggle('toggle-pemilik', chartPemilik);
        bindToggle('toggle-jenis', chartJenis);

        // ---------- Filter tabel ----------
        const fPemilik = document.getElementById('filterPemilik');
        const fKelas   = document.getElementById('filterKelas');
        const fJenis   = document.getElementById('filterJenis');

        function applyFilter() {
            stateFilter.pemilik = fPemilik.value;
            stateFilter.kelas   = fKelas.value;
            stateFilter.jenis   = fJenis.value;

            let tampil = 0;
            document.querySelectorAll('#tabelRs tbody tr').forEach(tr => {
                const ok = cocokDenganFilter({
                    pemilik: tr.dataset.pemilik,
                    kelas: tr.dataset.kelas,
                    jenis: tr.dataset.jenis,
                });
                tr.style.display = ok ? '' : 'none';
                if (ok) tampil++;
            });
            document.getElementById('jumlahTampil').textContent = tampil;

            updateAllCharts();

            // Terusan state filter ke peta Leaflet
            document.dispatchEvent(new CustomEvent('rs:filter', {
                detail: {
                    pemilik: fPemilik.value,
                    kelas: fKelas.value,
                    jenis: fJenis.value,
                }
            }));
        }

        [fPemilik, fKelas, fJenis].forEach(el => el.addEventListener('change', applyFilter));

        document.querySelectorAll('.btn-reset').forEach(a => {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                document.getElementById(this.dataset.target).value = '';
                applyFilter();
            });
        });
    });
</script>
@endsection

@push('scripts')
    <script type="application/json" id="data-rs">@json($daftarRs)</script>
    <script type="application/json" id="data-kelas">@json($kelas)</script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/Kesehatan/rumah-sakit-map.js') }}"></script>
@endpush