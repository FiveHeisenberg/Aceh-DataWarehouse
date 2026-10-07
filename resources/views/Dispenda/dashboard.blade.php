@php
    $rp = fn ($v) => 'Rp ' . number_format((float) ($v ?? 0), 0, ',', '.');
    // Server-side jumlah baris untuk tabel wilayah, tanpa prefix "Kabupaten "/"Kota ".
    $wilayahLabel = fn (?string $n) => preg_replace('/^(Kabupaten |Kota )\s*/i', '', (string) $n);
@endphp
@extends('layouts.app')

@push('styles')
    <meta name="geojson-url" content="{{ asset('aceh-regencies.json') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        .kpi-card, .panel-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
        }
        .panel-card { padding: 1.5rem; height: 100%; }
        .metric-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; }
        .metric-value { font-size: 1.5rem; font-weight: 700; color: #111827; }
        .panel-title { font-size: 1rem; font-weight: 600; color: #111827; }
        .panel-sub { font-size: .75rem; color: #6b7280; }
        .map-legend {
            position: absolute; bottom: 12px; left: 12px; z-index: 500;
            background: rgba(255, 255, 255, .93); border: 1px solid #e5e7eb;
            border-radius: .5rem; padding: .5rem .75rem; box-shadow: 0 1px 3px rgba(0,0,0,.1);
        }
        .map-gradient { height: 8px; width: 6rem; border-radius: 999px; background: linear-gradient(to right, rgb(254,240,138), rgb(249,115,22), rgb(220,38,38), rgb(127,29,29)); }
        .map-tip { font-size: .75rem; }
    </style>
@endpush

@section('content')
        <div class="d-flex flex-column gap-4 mx-auto" style="max-width: 1400px;">

            {{-- HEADER + FILTER --}}
            <form method="GET" action="{{ route('dispenda.dashboard') }}" id="filter-dashboard" class="d-flex flex-column flex-sm-row align-items-sm-start justify-content-between gap-3">
                <div>
                    <h1 class="h3 fw-bold mb-0" style="color: #111827;">Data Pendapatan Daerah Provinsi Aceh</h1>
                    <p class="mb-0 mt-1" style="font-size: .875rem; color: #6b7280;">Cakupan data: {{ $tahun }}, 23 kabupaten/kota.</p>
                </div>
                <div class="d-flex gap-2 flex-shrink-0">
                    <div>
                        <label for="filter-tahun" class="visually-hidden">Pilih tahun</label>
                        <select name="tahun" id="filter-tahun" class="form-select form-select-sm shadow-sm" data-autosubmit>
                            @foreach ($availableYears as $y)
                                <option value="{{ $y }}" @selected((int) $y === (int) $tahun)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            {{-- KPI CARDS --}}
            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="kpi-card p-3 h-100">
                        <p class="metric-label mb-0">Total Pendapatan</p>
                        <p class="metric-value mt-2 mb-0">{{ $rp($totalPendapatan) }}</p>
                        <p class="mb-0 mt-2" style="font-size: .875rem; color: #6b7280;">akumulasi pembayaran Lunas</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="kpi-card p-3 h-100">
                        <p class="metric-label mb-0">Target Realisasi</p>
                        <p class="metric-value mt-2 mb-0"><i class="bi bi-graph-up-arrow me-1" style="color: #0d9488;"></i>+{{ $targetRealisasi }}%</p>
                        <p class="mb-0 mt-2" style="font-size: .875rem; color: #6b7280;">dibanding tahun sebelumnya</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="kpi-card p-3 h-100">
                        <p class="metric-label mb-0">Total Tunggakan</p>
                        <p class="metric-value mt-2 mb-0"><i class="bi bi-exclamation-triangle me-1" style="color: #dc2626;"></i>{{ $rp($totalTunggakan) }}</p>
                        <p class="mb-0 mt-2" style="font-size: .875rem; color: #6b7280;">tagihan berstatus Belum Lunas</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="kpi-card p-3 h-100">
                        <p class="metric-label mb-0">Rasio Kepatuhan</p>
                        <p class="metric-value mt-2 mb-0"><i class="bi bi-percent me-1" style="color: #0d9488;"></i>{{ number_format((float) $rasioKepatuhan, 1, ',', '.') }}%</p>
                        <p class="mb-0 mt-2" style="font-size: .875rem; color: #6b7280;">tagihan Lunas dari total tagihan</p>
                    </div>
                </div>
            </div>

            {{-- PETA + TREN --}}
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    <div class="panel-card">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <h2 class="panel-title mb-0">Sebaran Pendapatan Daerah</h2>
                                <p class="panel-sub mb-0">Kepadatan pendapatan per kabupaten/kota</p>
                            </div>
                        </div>
                        <div class="position-relative">
                            <div id="peta-pendapatan" style="height: 420px; border-radius: .75rem; background-color: #e0f2fe;"></div>
                            <div class="map-legend">
                                <p class="mb-1" style="font-size: 10px; font-weight: 500; text-transform: uppercase; letter-spacing: .04em; color: #6b7280;">Pendapatan</p>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size: 10px; color: #6b7280;">Rendah</span>
                                    <div class="map-gradient"></div>
                                    <span style="font-size: 10px; color: #6b7280;">Tinggi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="panel-card">
                        <h2 class="panel-title mb-0">Tren Penerimaan Pajak</h2>
                        <p class="panel-sub mb-3">tagihan lunas per bulan jatuh tempo</p>
                        <div style="height: 356px;"><canvas id="chart-tren"></canvas></div>
                        <div class="mt-3 w-100">
                            <label for="filter-wilayah" class="panel-sub d-block mb-1">Tampilkan tren</label>
                            <select name="wilayah_id" id="filter-wilayah" form="filter-dashboard" class="form-select form-select-sm shadow-sm" data-autosubmit>
                                <option value="all">Seluruh Aceh (total)</option>
                                @foreach ($wilayahOptions as $w)
                                    <option value="{{ $w['id'] }}" @selected((string) $w['id'] === (string) $wilayahId)>{{ $wilayahLabel($w['name']) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TABEL WILAYAH --}}
            <div class="panel-card">
                <div class="d-flex flex-column flex-sm-row align-items-sm-start justify-content-between gap-3 mb-3">
                    <div>
                        <h2 class="panel-title mb-0">Detail Kabupaten / Kota</h2>
                        <p class="panel-sub mb-0" id="wilayah-count">{{ count($revenueByRegency) }} baris</p>
                    </div>
                    <div class="position-relative w-100" style="max-width: 256px;">
                        <label for="wilayah-search" class="visually-hidden">Cari wilayah</label>
                        <i class="bi bi-search position-absolute" style="left: .75rem; top: 50%; transform: translateY(-50%); color: #6b7280;"></i>
                        <input type="search" id="wilayah-search" class="form-control form-control-sm ps-5" placeholder="Cari wilayah...">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <caption class="visually-hidden">Total pendapatan pajak per kabupaten/kota tahun {{ $tahun }}</caption>
                        <thead>
                            <tr>
                                <th scope="col" class="text-uppercase" style="font-size: .75rem; color: #6b7280;">Kabupaten/Kota</th>
                                <th scope="col" class="text-uppercase" style="font-size: .75rem; color: #6b7280;">Tahun</th>
                                <th scope="col" class="text-end text-uppercase" style="font-size: .75rem; color: #6b7280;">Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody id="wilayah-tbody">
                            @forelse ($revenueByRegency as $row)
                                <tr data-nama="{{ strtolower($row['name']) }}">
                                    <td class="fw-medium" style="color: #111827;">{{ $row['name'] }}</td>
                                    <td style="color: #6b7280;">{{ $tahun }}</td>
                                    <td class="text-end fw-medium" style="color: #111827;">{{ $rp($row['total']) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-4" style="color: #6b7280;">Tidak ada data wilayah.</td></tr>
                            @endforelse
                            <tr id="wilayah-empty" hidden>
                                <td colspan="3" class="text-center py-4" style="color: #6b7280;">Tidak ada wilayah yang cocok.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
@endsection

@push('scripts')
<script type="application/json" id="data-revenue">@json($revenueByRegency)</script>
<script type="application/json" id="data-tren">@json($taxTrend)</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/Dispenda/filters.js') }}"></script>
<script src="{{ asset('js/Dispenda/dashboard.js') }}"></script>
@endpush
