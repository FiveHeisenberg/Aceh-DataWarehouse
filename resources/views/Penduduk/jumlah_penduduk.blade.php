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

<div class="d-flex flex-column" style="min-height: 100vh;">

    <!-- ==================== TOP NAVBAR ==================== -->
    <nav class="d-flex align-items-center px-4" style="height: 70px; background-color: #ffffff; border-bottom: 1px solid #e0e4f0; position: fixed; top: 0; left: 0; right: 0; z-index: 1100;">

        <!-- Logo -->
        <div class="d-flex align-items-center" style="width: 228px; flex-shrink: 0;">
            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background-color: #f0f0f0; border: 2px solid #d0d0d0; margin-right: 10px; flex-shrink: 0;">
                <span style="font-weight: 800; font-size: 16px; color: #1a1a2e;">A</span>
            </div>
            <div>
                <div style="font-weight: 800; font-size: 14px; color: #1a1a2e; line-height: 1.2;">Aceh Data<br>Warehouse</div>
            </div>
        </div>

        <!-- Back + Top Nav -->
        <div class="d-flex align-items-center">
            <a href="/" class="me-3 d-flex align-items-center" style="color: #5a6577;">
                <i class="bi bi-chevron-left" style="font-size: 18px;"></i>
            </a>
            <a href="/" class="text-decoration-none me-4" style="color: #1a1a2e; font-weight: 600; font-size: 15px;">Home</a>
            <a href="#" class="text-decoration-none" style="color: #5a6577; font-weight: 500; font-size: 15px;">About</a>
        </div>

        <!-- Spacer -->
        <div class="flex-grow-1"></div>

        <!-- Icons -->
        <div class="d-flex align-items-center" style="gap: 18px;">
            <i class="bi bi-bell" style="font-size: 19px; color: #5a6577;"></i>
            <i class="bi bi-gear" style="font-size: 19px; color: #5a6577;"></i>
            <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 34px; height: 34px; background-color: #eef2f9;">
                <i class="bi bi-person-fill" style="font-size: 18px; color: #5a6577;"></i>
            </div>
        </div>
    </nav>

    <div class="d-flex flex-grow-1" style="margin-top: 70px;">

        <!-- ==================== SIDEBAR ==================== -->
        <div class="p-3" style="width: 260px; background-color: #ffffff; border-right: 1px solid #e0e0e0; position: fixed; top: 70px; left: 0; bottom: 0; overflow-y: auto; z-index: 1000;">

            <!-- Kependudukan (Active) -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="background-color: #e8f5f0; color: #0d9488; border-right: 3px solid #0d9488;">
                    <i class="bi bi-people-fill me-2" style="font-size: 18px;"></i>
                    <span style="font-weight: 600; font-size: 14px;">Kependudukan</span>
                </a>
                <div class="ms-4 mt-1">
                    <a href="{{ route('penduduk.jumlah_penduduk') }}" class="d-block text-decoration-none py-1 px-2 rounded" style="background-color: #e8f5f0; color: #0d9488; font-weight: 600; font-size: 13px;">Jumlah Penduduk</a>
                    <a href="{{ route('penduduk.kartu_keluarga') }}" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Kartu keluarga</a>
                    <a href="#" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Pertumbuhan Penduduk</a>
                </div>
            </div>

            <!-- Pendidikan -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-mortarboard me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Pendidikan</span>
                </a>
            </div>

            <!-- Kesehatan -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-hospital me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Kesehatan</span>
                </a>
            </div>

            <!-- Sosial -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-people me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Sosial</span>
                </a>
            </div>

            <!-- Ketentraman dan Perlindungan -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-shield-check me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Ketentraman dan Perlindungan</span>
                </a>
            </div>

            <!-- Kebencanaan -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-exclamation-triangle me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Kebencanaan</span>
                </a>
            </div>

            <!-- Komunikasi dan Informasi -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-broadcast me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Komunikasi dan Informasi</span>
                </a>
            </div>

            <!-- Lingkungan dan Kehutanan -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-tree me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Lingkungan dan Kehutanan</span>
                </a>
            </div>

            <!-- Pemberdayaan Masyarakat -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-person-arms-up me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Pemberdayaan Masyarakat</span>
                </a>
            </div>

            <!-- Kebudayaan dan Pariwisata -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-flag me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Kebudayaan dan Pariwisata</span>
                </a>
            </div>

            <!-- Pekerjaan Umum dan Tata Ruang -->
            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-cone-striped me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Pekerjaan Umum dan Tata Ruang</span>
                </a>
            </div>

        </div>

        <!-- ==================== MAIN CONTENT ==================== -->
        <div class="flex-grow-1" style="margin-left: 260px;">
            <div class="p-4">

                <!-- Breadcrumb + Filter -->
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <nav style="font-size: 15px; color: #5a6577;">
                        <a href="/" class="text-decoration-none" style="color: #5a6577;">Home</a>
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
                                        <thead style="background-color: #eef2f9;" position: sticky; top: 0; z-index: 1;>
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

                <!-- ==================== ROW TAMBAHAN: Demografi Detail (Dummy Data) ==================== -->
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

            </div>
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