@extends('layouts.app')

@section('content')
@php
    // ====== DATA STATIS ======
    $totalRs = 85;

    $kelas = [
        ['label' => 'A',                 'jumlah' => 1,  'warna' => '#3b82f6'],
        ['label' => 'B',                 'jumlah' => 13, 'warna' => '#14b8a6'],
        ['label' => 'C',                 'jumlah' => 39, 'warna' => '#f59e0b'],
        ['label' => 'D',                 'jumlah' => 28, 'warna' => '#8b5cf6'],
        ['label' => 'D PRATAMA',         'jumlah' => 2,  'warna' => '#ef4444'],
        ['label' => 'Belum Ditetapkan',  'jumlah' => 2,  'warna' => '#06b6d4'],
    ];

    $pemilik = [
        ['label' => 'SWASTA/LAINNYA',  'jumlah' => 28],
        ['label' => 'Pemkab',          'jumlah' => 22],
        ['label' => 'Organisasi Sosial','jumlah' => 9],
        ['label' => 'Perusahaan',      'jumlah' => 7],
        ['label' => 'Pemkot',          'jumlah' => 4],
        ['label' => 'Pemprop',         'jumlah' => 3],
        ['label' => 'Perorangan',      'jumlah' => 3],
        ['label' => 'TNI AD',          'jumlah' => 3],
        ['label' => 'BUMN',            'jumlah' => 2],
        ['label' => 'Kementerian Lain','jumlah' => 2],
        ['label' => 'TNI AL',          'jumlah' => 1],
        ['label' => 'POLRI',           'jumlah' => 1],
    ];

    $jenis = [
        ['label' => 'RSU',       'jumlah' => 75],
        ['label' => 'RSIA',      'jumlah' => 6],
        ['label' => 'RSK Mata',  'jumlah' => 1],
        ['label' => 'RSK Bedah', 'jumlah' => 1],
        ['label' => 'RSK Jiwa',  'jumlah' => 1],
        ['label' => 'RSK GM',    'jumlah' => 1],
    ];

    $daftarRs = [
        ['nama' => 'RS Al Sabang',                 'provinsi' => 'Aceh', 'kota' => 'Kota Sabang',      'alamat' => 'Jl. Teuku Chik Ditiro',                          'telp' => '0852-0000-0001', 'pemilik' => 'Pemkot',         'kelas' => 'D',        'jenis' => 'RSU'],
        ['nama' => 'RS Arun Lhokseumawe',          'provinsi' => 'Aceh', 'kota' => 'Kota Lhokseumawe', 'alamat' => 'Jalan Plaju Ex Komplek Perumahan Arun',          'telp' => '0645-0000002',   'pemilik' => 'Perusahaan',     'kelas' => 'C',        'jenis' => 'RSU'],
        ['nama' => 'RS Bhayangkara Banda Aceh',    'provinsi' => 'Aceh', 'kota' => 'Kota Banda Aceh',  'alamat' => 'Jl. Cut Nyak Dhien No. 23',                      'telp' => '0651-0000003',   'pemilik' => 'POLRI',          'kelas' => 'C',        'jenis' => 'RSU'],
        ['nama' => 'RS Cahaya Sehat',              'provinsi' => 'Aceh', 'kota' => 'Aceh Selatan',     'alamat' => 'Jl. Panglima Raja Lelo, No. 10',                 'telp' => '(0656) 000004',  'pemilik' => 'SWASTA/LAINNYA', 'kelas' => 'D',        'jenis' => 'RSU'],
        ['nama' => 'RS Cut Nyak Dhien Langsa',     'provinsi' => 'Aceh', 'kota' => 'Kota Langsa',      'alamat' => 'Jl. T.M. Bachrun No.1. Kabupaten Kota Langsa Provinsi Aceh', 'telp' => '0641-0000005', 'pemilik' => 'Pemkot', 'kelas' => 'C', 'jenis' => 'RSU'],
        ['nama' => 'RS Gayo Medical Centre',       'provinsi' => 'Aceh', 'kota' => 'Aceh Tengah',      'alamat' => 'Jalan Paya Tumpi Mendale nomor 126',             'telp' => '0821-0000-0006', 'pemilik' => 'SWASTA/LAINNYA', 'kelas' => 'D',        'jenis' => 'RSU'],
        ['nama' => 'RS Gigi Mulut Universitas Syiah Kuala', 'provinsi' => 'Aceh', 'kota' => 'Kota Banda Aceh', 'alamat' => 'Jl. Prof A. Madjid Ibrahim I No. 5', 'telp' => '0651-0000007',   'pemilik' => 'Pemprop',        'kelas' => 'Belum Ditetapkan', 'jenis' => 'RSK GM'],
        ['nama' => 'RS Hermina Aceh',              'provinsi' => 'Aceh', 'kota' => 'Aceh Besar',       'alamat' => 'Jl. Soekarno Hatta',                             'telp' => '0821-0000-0008', 'pemilik' => 'SWASTA/LAINNYA', 'kelas' => 'C',        'jenis' => 'RSU'],
    ];
@endphp

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
    #map { height: 300px; background: #f8fafc; }
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

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelRs">
                <thead>
                    <tr>
                        <th>Rumah Sakit</th>
                        <th>Provinsi</th>
                        <th>Kab/Kota</th>
                        <th>Alamat</th>
                        <th>Telp</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daftarRs as $rs)
                        <tr data-pemilik="{{ $rs['pemilik'] }}" data-kelas="{{ $rs['kelas'] }}" data-jenis="{{ $rs['jenis'] }}">
                            <td>{{ $rs['nama'] }}</td>
                            <td>{{ $rs['provinsi'] }}</td>
                            <td>{{ $rs['kota'] }}</td>
                            <td>{{ $rs['alamat'] }}</td>
                            <td>{{ $rs['telp'] }}</td>
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
        const total       = {{ $totalRs }};

        // ---------- Donut kelas ----------
        new ApexCharts(document.querySelector('#chartKelas'), {
            chart: { type: 'donut', height: 300 },
            series: dataKelas.map(d => d.jumlah),
            labels: dataKelas.map(d => d.label),
            colors: dataKelas.map(d => d.warna),
            legend: { position: 'bottom', fontSize: '11px' },
            dataLabels: { enabled: true, formatter: (v, o) => o.w.config.series[o.seriesIndex] },
            plotOptions: {
                pie: { donut: { size: '62%', labels: {
                    show: true,
                    total: { show: true, label: 'Total RS', formatter: () => total }
                } } }
            }
        }).render();

        // ---------- Chart pemilik & jenis (bisa ganti tipe) ----------
        function buildOptions(type, data) {
            const labels = data.map(d => d.label);
            const values = data.map(d => d.jumlah);

            if (type === 'donut') {
                return {
                    chart: { type: 'donut', height: 360, events: chartEvents },
                    series: values,
                    labels: labels,
                    colors: palette,
                    legend: { position: 'bottom', fontSize: '11px' }
                };
            }

            return {
                chart: { type: 'bar', height: 360, toolbar: { show: true }, events: chartEvents },
                series: [{ name: 'Jumlah RS', data: values }],
                xaxis: { categories: labels, title: { text: type === 'bar' ? 'Jumlah RS' : undefined } },
                yaxis: { title: { text: type === 'column' ? 'Jumlah RS' : undefined } },
                plotOptions: { bar: { horizontal: type === 'bar', distributed: true, borderRadius: 4, columnWidth: '55%' } },
                colors: palette,
                legend: { show: false },
                dataLabels: { enabled: true, style: { colors: ['#0f172a'] }, offsetX: type === 'bar' ? 20 : 0 }
            };
        }

        // event klik: set filter pada select tabel
        let chartEvents = {};
        function makeCharts(selector, data, initialType, selectId) {
            chartEvents = {
                dataPointSelection: function (e, chart, cfg) {
                    const label = data[cfg.dataPointIndex].label;
                    const select = document.getElementById(selectId);
                    select.value = select.value === label ? '' : label;
                    applyFilter();
                }
            };
            let chart = new ApexCharts(document.querySelector(selector), buildOptions(initialType, data));
            chart.render();
            return {
                switchType(type) {
                    chartEvents = {
                        dataPointSelection: function (e, c, cfg) {
                            const label = data[cfg.dataPointIndex].label;
                            const select = document.getElementById(selectId);
                            select.value = select.value === label ? '' : label;
                            applyFilter();
                        }
                    };
                    chart.destroy();
                    chart = new ApexCharts(document.querySelector(selector), buildOptions(type, data));
                    chart.render();
                }
            };
        }

        const chartPemilik = makeCharts('#chartPemilik', dataPemilik, 'bar', 'filterPemilik');
        const chartJenis   = makeCharts('#chartJenis',   dataJenis,   'column', 'filterJenis');

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
            let tampil = 0;
            document.querySelectorAll('#tabelRs tbody tr').forEach(tr => {
                const ok = (!fPemilik.value || tr.dataset.pemilik === fPemilik.value)
                        && (!fKelas.value   || tr.dataset.kelas   === fKelas.value)
                        && (!fJenis.value   || tr.dataset.jenis   === fJenis.value);
                tr.style.display = ok ? '' : 'none';
                if (ok) tampil++;
            });
            document.getElementById('jumlahTampil').textContent = tampil;
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