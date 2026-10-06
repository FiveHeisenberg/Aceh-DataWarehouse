@extends('layouts.app')

@section('content')

<meta name="geojson-url" content="{{ asset('js/Penduduk/aceh-kabupaten.geojson') }}">

<!-- Breadcrumb + Filter -->
<div class="d-flex align-items-center justify-content-between mb-4">
    <nav style="font-size: 15px; color: #5a6577;">
        <a href="/index" class="text-decoration-none" style="color: #5a6577;">Home</a>
        <span class="mx-1">&gt;</span>
        <span>Kependudukan</span>
        <span class="mx-1">&gt;</span>
        <span style="color: #1a1a2e; font-weight: 600;">Jumlah Penduduk</span>
    </nav>
    <select id="filter-tahun" class="form-select" style="width: 150px; border: 1px solid #d0d8e0; border-radius: 6px; font-size: 14px;">
        <option value="2" selected>Memuat...</option>
    </select>
</div>

<!-- Two Column Layout -->
<div class="row g-4">

    <!-- LEFT COLUMN: Stat cards + Map -->
    <div class="col-lg-8">

        <!-- Summary Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    <div class="card-body p-4">
                        <div class="text-uppercase mb-2" style="font-size: 12px; font-weight: 700; color: #5a6577; letter-spacing: 0.5px;">Total Penduduk</div>
                        <div class="d-flex align-items-baseline mb-1">
                            <strong id="stat-total" style="font-size: 32px; font-weight: 800; color: #1a1a2e;">-</strong>
                        </div>
                        <small id="stat-total-satuan" style="color: #8892a4; font-size: 13px;">dalam jiwa, tahun ...</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    <div class="card-body p-4">
                        <div class="text-uppercase mb-2" style="font-size: 12px; font-weight: 700; color: #5a6577; letter-spacing: 0.5px;">Laju Pertumbuhan</div>
                        <div class="d-flex align-items-baseline mb-1">
                            <strong id="stat-pertumbuhan" style="font-size: 32px; font-weight: 800; color: #1a1a2e;">-</strong>
                            <span class="ms-3" style="font-size: 14px; color: #5a6577;">Per Tahun</span>
                        </div>
                        <small style="color: #8892a4; font-size: 13px;">dibanding tahun sebelumnya</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Panel -->
        <div class="card" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div class="card-header border-0 d-flex align-items-center justify-content-between p-3" style="background-color: #ffffff; border-radius: 12px 12px 0 0;">
                <h2 class="mb-0" style="font-weight: 700; color: #1a1a2e; font-size: 18px;">Sebaran Penduduk</h2>
                <button class="btn btn-sm btn-light" type="button" style="border-radius: 6px;">
                    <i class="bi bi-arrows-fullscreen" style="font-size: 16px; color: #5a6577;"></i>
                </button>
            </div>
            <div class="card-body p-0">
                <div id="aceh-map" style="height: 450px; border-radius: 0 0 12px 12px;"></div>
            </div>
        </div>

    </div>

    <!-- RIGHT COLUMN: Trend chart + Detail table -->
    <div class="col-lg-4">

        <!-- Trend Panel -->
        <div class="card mb-4" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div class="card-header border-0 p-3" style="background-color: #ffffff; border-radius: 12px 12px 0 0;">
                <h2 class="mb-0" style="font-weight: 700; color: #1a1a2e; font-size: 18px;">Tren Pertumbuhan</h2>
            </div>
            <div class="card-body p-3">
                <canvas id="trendChart" style="width: 100%; height: 200px;"></canvas>
            </div>
            <div class="p-3" style="background-color: #ffffff; border-top: 1px solid #e0e4f0;">
                <label class="form-label mb-1" style="font-size: 13px; font-weight: 600; color: #5a6577;">Tampilkan tren</label>
                <select id="filter-trend" class="form-select form-select-sm" style="border-radius: 6px; border: 1px solid #d0d8e0;">
                    <option value="">Seluruh Aceh (total)</option>
                </select>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="card" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div class="card-header border-0 pt-3" style="background-color: #ffffff; border-radius: 12px 12px 0 0;">
                <h2 class="mb-1" style="font-weight: 700; color: #1a1a2e; font-size: 18px;">Detail Kabupaten/Kota</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: calc(34px * 6 + 32px); overflow-y: auto;">
                    <table class="table table-hover mb-0" style="font-size: 14px;">
                        <thead style="background-color: #eef2f9; position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th class="px-3 py-1" style="font-weight: 700; color: #1a1a2e; border-bottom: 1px solid #d8dde8;">Kota/Kabupaten</th>
                                <th class="px-3 py-1 text-end" style="font-weight: 700; color: #1a1a2e; border-bottom: 1px solid #d8dde8;">
                                    <button class="btn btn-sm p-0" style="color: #1a1a2e; font-weight: 700;">
                                        Tahun
                                    </button>
                                </th>
                                <th class="px-3 py-1 text-end" style="font-weight: 700; color: #1a1a2e; border-bottom: 1px solid #d8dde8;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ==================== ROW TAMBAHAN: Demografi Detail ==================== -->
<div class="row g-4 mt-1">

    <!-- Struktur Kelompok Umur -->
    <div class="col-lg-6">
        <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div class="card-header border-0 p-3" style="background-color: #ffffff; border-radius: 12px 12px 0 0;">
                <h2 class="mb-0" style="font-weight: 700; color: #1a1a2e; font-size: 18px;">Struktur Kelompok Umur</h2>
            </div>
            <div class="card-body p-3 pt-0" id="struktur-umur-container">
                <div class="text-center py-4 text-muted">Memuat data...</div>
            </div>
        </div>
    </div>

    <!-- Kategori Kelompok Umur (Piramida Penduduk) -->
    <div class="col-lg-6">
        <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div class="card-header border-0 d-flex align-items-center justify-content-between p-3" style="background-color: #ffffff; border-radius: 12px 12px 0 0;">
                <h2 class="mb-0" style="font-weight: 700; color: #1a1a2e; font-size: 18px;">Kategori Umur Berdasarkan Jenis Kelamin</h2>
                <div class="d-flex align-items-center" style="gap: 14px; font-size: 12px; color: #5a6577;">
                    <span><span style="display:inline-block;width:10px;height:10px;background-color:#2563a8;border-radius:2px;margin-right:5px;"></span><span id="legend-total-l">Laki-Laki (-)</span></span>
                    <span><span style="display:inline-block;width:10px;height:10px;background-color:#0d9488;border-radius:2px;margin-right:5px;"></span><span id="legend-total-p">Perempuan (-)</span></span>
                </div>
            </div>
            <div class="card-body p-3">
                <div style="height: 360px">
                    <canvas id='pyramidChart'></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Perkawinan -->
    <div class="col-lg-6">
        <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div class="card-header border-0 p-3" style="background-color: #ffffff; border-radius: 12px 12px 0 0;">
                <h2 class="mb-0" style="font-weight: 700; color: #1a1a2e; font-size: 18px;">Status Perkawinan</h2>
            </div>
            <div class="card-body p-3 pt-0">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 h-100" style="background-color: #f4f6fb; border-radius: 10px;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase" style="font-size: 11px; font-weight: 700; color: #5a6577; letter-spacing: 0.4px;">Sudah Kawin</span>
                            </div>
                            <div id="stat-sudah-kawin" style="font-weight: 800; color: #1a1a2e; font-size: 22px;">Memuat . . .</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 h-100" style="background-color: #f4f6fb; border-radius: 10px;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase" style="font-size: 11px; font-weight: 700; color: #5a6577; letter-spacing: 0.4px;">Belum Kawin</span>
                            </div>
                            <div id="stat-belum-kawin" style="font-weight: 800; color: #1a1a2e; font-size: 22px;">Memuat . . .</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 h-100" style="background-color: #f4f6fb; border-radius: 10px;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase" style="font-size: 11px; font-weight: 700; color: #5a6577; letter-spacing: 0.4px;">Cerai Mati</span>
                            </div>
                            <div id="stat-cerai-mati" style="font-weight: 800; color: #1a1a2e; font-size: 22px;">Memuat . . .</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 h-100" style="background-color: #f4f6fb; border-radius: 10px;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-uppercase" style="font-size: 11px; font-weight: 700; color: #5a6577; letter-spacing: 0.4px;">Cerai Hidup</span>
                            </div>
                            <div id="stat-cerai-hidup" style="font-weight: 800; color: #1a1a2e; font-size: 22px;">Memuat . . .</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Komposisi Agama -->
    <div class="col-lg-6">
        <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div class="card-header border-0 p-3" style="background-color: #ffffff; border-radius: 12px 12px 0 0;">
                <h2 class="mb-0" style="font-weight: 700; color: #1a1a2e; font-size: 18px;">Komposisi Agama</h2>
            </div>

            <div class="card-body p-3 pt-0">
                <div class="p-3 pt-0" id="agama-container">
                    <div class="text-center py-4 text-muted">Memuat Data . . .</div>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Footer -->
<footer class="mt-4 pb-4">
    <div class="d-flex align-items-center justify-content-between" style="border-top: 1px solid #e0e4f0; padding-top: 20px;">
        <div style="font-size: 13px; color: #8892a4;">
            Portal Data Warehouse Provinsi Aceh
        </div>
        <div style="font-size: 13px; color: #8892a4;">
            Diskominfo Aceh — Data Terintegrasi
        </div>
    </div>
</footer>

<!-- Scripts -->
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JS Utama (Menangani API Index, Tabel, Chart Tren) -->
<script src="{{ asset('js/Penduduk/jumlah_penduduk.js') }}"></script>

<!-- Custom JS Peta (Menangani Leaflet & Choropleth) -->
<script src="{{ asset('js/Penduduk/map-leaflet.js') }}"></script>

@endsection
