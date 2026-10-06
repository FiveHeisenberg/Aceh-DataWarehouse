@php
    $rp = fn ($v) => 'Rp ' . number_format((float) ($v ?? 0), 0, ',', '.');
    // Server-side jumlah baris untuk tabel wilayah, tanpa prefix "Kabupaten "/"Kota ".
    $wilayahLabel = fn (?string $n) => preg_replace('/^(Kabupaten |Kota )\s*/i', '', (string) $n);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="geojson-url" content="{{ asset('aceh-regencies.json') }}">
    <title>Ringkasan Pendapatan Daerah - Aceh Data Warehouse</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        body { background-color: #f8f9fc; }
        .kpi-card, .panel-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
        }
        .panel-card { padding: 1.5rem; height: 100%; }
        .sidebar-nav-item {
            display: flex; align-items: center; gap: .75rem;
            padding: .625rem .75rem; border-radius: .5rem;
            font-size: .875rem; font-weight: 500; text-decoration: none;
            border-left: 4px solid transparent; color: #374151;
        }
        .sidebar-nav-item:hover { background-color: #f3f4f6; }
        .sidebar-nav-item.active { background-color: #ccfbf1; color: #0f766e; border-left-color: #0d9488; }
        .sidebar-sub-item {
            display: block; padding: .5rem .75rem; border-radius: .375rem;
            font-size: .875rem; text-decoration: none; color: #6b7280;
        }
        .sidebar-sub-item:hover { background-color: #f3f4f6; color: #111827; }
        .sidebar-sub-item.active { background-color: #f0fdfa; color: #0f766e; font-weight: 500; }
        .metric-label { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; }
        .metric-value { font-size: 1.5rem; font-weight: 700; color: #111827; }
        .panel-title { font-size: 1rem; font-weight: 600; color: #111827; }
        .panel-sub { font-size: .75rem; color: #6b7280; }
        .map-legend {
            position: absolute; bottom: 12px; left: 12px; z-index: 500;
            background: rgba(255, 255, 255, .93); border: 1px solid #e5e7eb;
            border-radius: .5rem; padding: .5rem .75rem; box-shadow: 0 1px 3px rgba(0,0,0,.1);
        }
        .map-gradient { height: 8px; width: 6rem; border-radius: 999px; background: linear-gradient(to right, #d1fae5, #0f766e); }
        .map-tip { font-size: .75rem; }
    </style>
</head>
<body>

<div class="d-flex" style="min-height: 100vh;">

    {{-- ==================== SIDEBAR ==================== --}}
    <aside class="d-flex flex-column flex-shrink-0 bg-white border-end" style="width: 288px; position: sticky; top: 0; height: 100vh; overflow-y: auto;">
        <div class="d-flex align-items-center gap-3 px-4 py-4">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-dark text-white fw-bold flex-shrink-0" style="width: 40px; height: 40px;">A</div>
            <div class="min-w-0">
                <p class="mb-0 text-truncate fw-bold" style="font-size: .875rem; color: #111827;">Aceh Data Warehouse</p>
                <p class="mb-0 text-truncate" style="font-size: .75rem; color: #6b7280;">Provinsi Aceh</p>
            </div>
        </div>

        <nav class="d-flex flex-column gap-1 px-3 pt-2" aria-label="Navigasi utama">
            <a href="#" class="sidebar-nav-item"><i class="bi bi-people-fill" style="font-size: 18px;"></i> Penduduk</a>
            <a href="#" class="sidebar-nav-item"><i class="bi bi-heart-fill" style="font-size: 18px;"></i> Sosial</a>
            <a href="#" class="sidebar-nav-item"><i class="bi bi-activity" style="font-size: 18px;"></i> Kesehatan</a>
            <a href="#" class="sidebar-nav-item"><i class="bi bi-mortarboard-fill" style="font-size: 18px;"></i> Pendidikan</a>
            <a href="#" class="sidebar-nav-item active" aria-current="page"><i class="bi bi-bank" style="font-size: 18px;"></i> Pendapatan Daerah</a>

            <ul class="list-unstyled mt-1 mb-1 ps-4" aria-label="Sub-menu Dispenda">
                <li><a href="{{ route('dispenda.dashboard') }}" class="sidebar-sub-item active" aria-current="page">Ringkasan Pendapatan</a></li>
                <li><a href="{{ route('dispenda.tagihan') }}" class="sidebar-sub-item">Data Tagihan</a></li>
                <li><a href="{{ route('dispenda.objek-pajak') }}" class="sidebar-sub-item">Objek Pajak</a></li>
            </ul>
        </nav>

        <div class="mt-auto px-4 py-4">
            <p class="mb-0" style="font-size: .75rem; color: #6b7280;">Badan Pengelolaan Keuangan Aceh</p>
            <p class="mb-0" style="font-size: .75rem; color: #9ca3af;">v2026.1</p>
        </div>
    </aside>

    {{-- ==================== MAIN ==================== --}}
    <main class="flex-grow-1 overflow-x-hidden px-4 px-lg-5 py-4">
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
                        <div style="height: 260px;"><canvas id="chart-tren"></canvas></div>
                        <div class="mt-3" style="max-width: 256px;">
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
    </main>
</div>

<script type="application/json" id="data-revenue">@json($revenueByRegency)</script>
<script type="application/json" id="data-tren">@json($taxTrend)</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/Dispenda/filters.js') }}"></script>
<script src="{{ asset('js/Dispenda/dashboard.js') }}"></script>

</body>
</html>
