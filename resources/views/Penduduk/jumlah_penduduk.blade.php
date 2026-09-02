<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="geojson-url" content="{{ asset('js/Penduduk/aceh-kabupaten.geojson') }}">
    <title>Data Kependudukan Provinsi Aceh</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
</head>
<body style="background-color: #f8f9fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

<div class="d-flex" style="min-height: 100vh;">

    <!-- ==================== SIDEBAR ==================== -->
    <div class="d-flex flex-column" style="width: 260px; background-color: #ffffff; border-right: 1px solid #e0e0e0; position: fixed; top: 0; left: 0; bottom: 0; z-index: 1000;">

        <!-- Logo Section -->
        <div class="d-flex align-items-center p-3" style="border-bottom: 1px solid #e8e8e8;">
            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 45px; height: 45px; background-color: #f0f0f0; border: 2px solid #d0d0d0; margin-right: 12px; flex-shrink: 0;">
                <span style="font-weight: 800; font-size: 18px; color: #1a1a2e;">A</span>
            </div>
            <div>
                <div style="font-weight: 800; font-size: 16px; color: #1a1a2e; line-height: 1.2;">Aceh Data<br>Warehouse</div>
                <div style="font-size: 12px; color: #888;">Provinsi Aceh</div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="flex-grow-1 p-3" style="overflow-y: auto;">

            <!-- Penduduk (Active) -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="background-color: #e8f5f0; color: #0d9488; border-right: 3px solid #0d9488;">
                    <i class="bi bi-people-fill me-2" style="font-size: 18px;"></i>
                    <span style="font-weight: 600; font-size: 14px;">Penduduk</span>
                </a>
                <div class="ms-4 mt-1">
                    <a href="{{ route('penduduk.jumlah_penduduk') }}" class="d-block text-decoration-none py-1 px-2 rounded" style="background-color: #e8f5f0; color: #0d9488; font-weight: 600; font-size: 13px;">Jumlah Penduduk</a>
                    <a href="#" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Kepadatan Penduduk</a>
                    <a href="#" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Pertumbuhan Penduduk</a>
                </div>
            </div>

            <!-- Sosial -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-people me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Sosial</span>
                </a>
            </div>

            <!-- Kesehatan -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-hospital me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Kesehatan</span>
                </a>
            </div>

            <!-- Pendidikan -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-mortarboard me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Pendidikan</span>
                </a>
            </div>

        </div>
    </div>

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="flex-grow-1" style="margin-left: 260px;">

        <!-- Header -->
        <header class="px-4 py-3" style="background-color: #ffffff; border-bottom: 1px solid #e0e4f0;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="mb-1" style="font-weight: 800; color: #1a1a2e; font-size: 24px;">Data Kependudukan Provinsi Aceh</h1>
                    <p class="mb-0" style="font-size: 13px; color: #8892a4;">Cakupan data: 2021–2023, 23 kabupaten/kota.</p>
                </div>
                <div>
                    <select id="filter-tahun" class="form-select" style="width: 150px; border: 1px solid #d0d8e0; border-radius: 6px; font-size: 14px;">
                        <option value="2" selected>Memuat...</option>
                    </select>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="p-4">

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

            <!-- Map and Trend Section -->
            <div class="row g-4 mb-4">
                <!-- Map Panel -->
                <div class="col-lg-8">
                    <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                        <div class="card-header border-0 d-flex align-items-center justify-content-between p-3" style="background-color: #ffffff; border-radius: 12px 12px 0 0;">
                            <h2 class="mb-0" style="font-weight: 700; color: #1a1a2e; font-size: 18px;">Sebaran Penduduk</h2>
                            <button class="btn btn-sm btn-light" type="button" style="border-radius: 6px;">
                                <i class="bi bi-arrows-fullscreen" style="font-size: 16px; color: #5a6577;"></i>
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div id="aceh-map" style="height: 450px; border-radius: 0 0 12px 12px;"></div>
                        </div>
                        <!-- <div class="p-3" style="background-color: #ffffff; border-top: 1px solid #e0e4f0; border-radius: 0 0 12px 12px;">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <label class="form-label mb-1" style="font-size: 13px; font-weight: 600; color: #5a6577;">Kabupaten/Kota</label>
                                    <select class="form-select form-select-sm" style="border-radius: 6px; border: 1px solid #d0d8e0;">
                                        <option value="">Semua kabupaten/kota</option>
                                        <option>Aceh Utara</option>
                                        <option>Bireuen</option>
                                        <option>Pidie</option>
                                        <option>Aceh Timur</option>
                                        <option>Aceh Besar</option>
                                    </select>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>

                <!-- Trend Panel -->
                <div class="col-lg-4">
                    <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                        <div class="card-header border-0 p-3" style="background-color: #ffffff; border-radius: 12px 12px 0 0;">
                            <h2 class="mb-0" style="font-weight: 700; color: #1a1a2e; font-size: 18px;">Tren Pertumbuhan</h2>
                        </div>
                        <div class="card-body p-3">
                            <canvas id="trendChart" style="width: 100%; height: 200px;"></canvas>
                        </div>
                        <div class="p-3" style="background-color: #ffffff; border-top: 1px solid #e0e4f0;">
                            <label class="form-label mb-1" style="font-size: 13px; font-weight: 600; color: #5a6577;">Tampilkan tren</label>
                            <select class="form-select form-select-sm" style="border-radius: 6px; border: 1px solid #d0d8e0;">
                                <option value="">Seluruh Aceh (total)</option>
                                <option>Aceh Utara</option>
                                <option>Bireuen</option>
                                <option>Pidie</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Table -->
            <div class="card" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                <div class="card-header border-0 d-flex align-items-center justify-content-between p-3" style="background-color: #ffffff; border-radius: 12px 12px 0 0;">
                    <div>
                        <h2 class="mb-1" style="font-weight: 700; color: #1a1a2e; font-size: 18px;">Detail Kabupaten / Kota</h2>
                        <small id="table-note" style="color: #8892a4; font-size: 13px;">-</small>
                    </div>
                    <div class="position-relative" style="width: 250px;">
                        <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #8892a4;"></i>
                        <input id="table-search" type="search" class="form-control ps-5" placeholder="Cari wilayah..." style="border-radius: 6px; border: 1px solid #d0d8e0; font-size: 14px;">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size: 14px;">
                            <thead style="background-color: #eef2f9;">
                                <tr>
                                    <th class="px-4 py-3" style="font-weight: 700; color: #1a1a2e; border-bottom: 1px solid #d8dde8;">Kabupaten/Kota</th>
                                    <th class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e; border-bottom: 1px solid #d8dde8;">
                                        <button class="btn btn-sm p-0" style="color: #1a1a2e; font-weight: 700;">
                                            Tahun <i class="bi bi-arrow-down-up ms-1"></i>
                                        </button>
                                    </th>
                                    <th class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e; border-bottom: 1px solid #d8dde8;">Jumlah</th>
                                    <th class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e; border-bottom: 1px solid #d8dde8;">Satuan</th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
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

        </div>
    </div>
</div>

<!-- Scripts -->
<!-- 1. Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- 2. Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- 3. Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- 4. Custom JS Utama (Menangani API Index, Tabel, Chart Tren) -->
<script src="{{ asset('js/penduduk/jumlah_penduduk.js') }}"></script>

<!-- 5. Custom JS Peta (Menangani Leaflet & Choropleth) -->
<script src="{{ asset('js/penduduk/map-leaflet.js') }}"></script>

</body>
</html>