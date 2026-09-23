<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Keluarga - Aceh Data Warehouse</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @media (max-width: 1199.98px) {
            .sidebar-fixed {
                position: relative !important;
                width: 100% !important;
                min-height: auto !important;
            }
        }

        @media (max-width: 767.98px) {
            .table-responsive {
                font-size: 13px;
            }

            #stat-total-kk {
                font-size: 30px !important;
            }

            .card-body {
                padding: 1.25rem !important;
            }
        }
    </style>
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
                    <span style="font-weight: 600; font-size: 14px;">Penduduk</span>
                </a>
                <div class="ms-4 mt-1">
                    <a href="{{ route('penduduk.jumlah_penduduk') }}" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Jumlah Penduduk</a>
                    <a href="{{ route('penduduk.kartu_keluarga') }}" class="d-block text-decoration-none py-1 px-2 rounded" style="background-color: #e8f5f0; color: #0d9488; font-weight: 600; font-size: 13px;">Kartu Keluarga</a>
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
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <nav style="font-size: 15px; color: #5a6577;">
                        <a href="/" class="text-decoration-none" style="color: #5a6577;">Home</a>
                        <span class="mx-1">&gt;</span>
                        <span>Penduduk</span>
                        <span class="mx-1">&gt;</span>
                        <span style="color: #1a1a2e; font-weight: 600;">Kartu Keluarga</span>
                    </nav>
                    <select id="filter-tahun" class="form-select" style="width: 150px; border: 1px solid #d0d8e0; border-radius: 6px; font-size: 14px;">
                        <option value="">Memuat...</option>
                    </select>
                </div>

                <h1 class="mb-1" style="font-weight: 800; color: #1a1a2e; font-size: 24px;">Analisis Data Kartu Keluarga (KK) Provinsi Aceh</h1>
                <p id="cakupan-data" class="mb-4" style="font-size: 13px; color: #8892a4;">&nbsp;</p>

                <!-- ==================== DASHBOARD GRID ==================== -->
                <div class="row g-4 mb-4 align-items-stretch">

                    <!-- ==================== KOLOM KIRI ==================== -->
                    <div class="col-xl-7">

                        <!-- Summary Cards -->
                        <div class="row g-4 mb-4">

                            <!-- Card 1: Total Kartu Keluarga -->
                            <div class="col-md-6">
                                <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span class="text-uppercase" style="font-size: 12px; font-weight: 700; color: #5a6577; letter-spacing: 1px;">Total Kartu Keluarga</span>
                                            <span id="stat-kk-year-badge" class="badge rounded-pill" style="background-color: #e8f0ff; color: #1a1a2e; font-size: 11px; font-weight: 600; padding: 4px 10px;">—</span>
                                        </div>
                                        <div class="mb-3">
                                            <strong id="stat-total-kk" style="font-size: 38px; font-weight: 800; color: #1a1a2e; letter-spacing: -1px;">—</strong>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between pt-3" style="border-top: 1px solid #e8e8e8;">
                                            <span id="stat-kk-growth" class="badge rounded-pill d-none" style="background-color: #e8f5f0; color: #0d9488; font-size: 13px; font-weight: 700; padding: 6px 12px;">
                                                <i id="stat-kk-growth-icon" class="bi bi-arrow-up-short me-1" style="font-size: 16px;"></i>
                                                <span id="stat-kk-growth-value">—</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: KK Tertinggi -->
                            <div class="col-md-6">
                                <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <span class="text-uppercase" style="font-size: 12px; font-weight: 700; color: #5a6577; letter-spacing: 1px;">Jumlah KK Terbanyak</span>
                                        </div>
                                        <div class="mb-3">
                                            <strong id="stat-kk-terbanyak-nama" style="font-size: 22px; font-weight: 800; color: #1a1a2e;">-</strong>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between pt-3" style="border-top: 1px solid #e8e8e8;">
                                            <span id="stat-kk-terbanyak-jumlah" class="badge rounded-pill" style="background-color: #e8f5f0; color: #0d9488; font-size: 13px; font-weight: 700; padding: 6px 12px;">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chart: Tren Pertumbuhan KK -->
                        <div class="card" style="border: 1px solid #e0e4f0; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h2 class="mb-0" style="font-weight: 800; color: #1a1a2e; font-size: 20px;">Tren Pertumbuhan KK Provinsi Aceh</h2>
                                </div>
                                <p class="mb-4" style="font-size: 13px; color: #8892a4;">Evolusi jumlah Kepala Keluarga per tahun dari data warehouse</p>

                                <div style="height: 320px;">
                                    <canvas id="trenKKChart"></canvas>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ==================== KOLOM KANAN ==================== -->
                    <div class="col-xl-5">
                        <!-- Chart: Distribusi Wilayah -->
                        <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h2 class="mb-0" style="font-weight: 800; color: #1a1a2e; font-size: 20px;">Distribusi Wilayah</h2>
                                </div>
                                <p id="distribusi-subtitle" class="mb-4" style="font-size: 13px; color: #8892a4;">&nbsp;</p>

                                <div id="distribusi-container">
                                    <div class="text-center py-4 text-muted">Memuat data...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Card -->
                <div class="card" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    <div class="card-header border-0 d-flex align-items-center justify-content-between p-4" style="background-color: #ffffff; border-radius: 12px 12px 0 0;">
                        <div>
                            <h2 class="mb-1" style="font-weight: 700; color: #1a1a2e; font-size: 18px;">Tabel Rincian Data KK per Kabupaten / Kota</h2>
                            <p id="tabel-subtitle" class="mb-0" style="font-size: 13px; color: #8892a4;">&nbsp;</p>
                        </div>
                        <div class="position-relative" style="width: 280px;">
                            <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #8892a4;"></i>
                            <input id="kk-search" type="search" class="form-control ps-5" placeholder="Cari nama Kab/Kota..." style="border-radius: 6px; border: 1px solid #d0d8e0; font-size: 14px;">
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="font-size: 14px;">
                                <thead style="background-color: #eef2f9;">
                                    <tr>
                                        <th class="px-4 py-3" style="font-weight: 700; color: #1a1a2e; border-bottom: 1px solid #d8dde8; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Kabupaten / Kota</th>
                                        <th id="kk-table-year-head" class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e; border-bottom: 1px solid #d8dde8; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Jumlah KK</th>
                                    </tr>
                                </thead>
                                <tbody id="kk-table-body">
                                    <tr>
                                        <td colspan="2" class="text-center py-4 text-muted">Memuat data...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer border-0 d-flex align-items-center justify-content-between p-4" style="background-color: #ffffff; border-radius: 0 0 12px 12px;">
                        <div style="font-size: 13px; color: #5a6577;">
                            Menampilkan <strong id="kk-show-count" style="color: #1a1a2e;">0</strong> dari <strong id="kk-total-count" style="color: #1a1a2e;">0</strong> Daerah Kabupaten/Kota
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JS Kartu Keluarga -->
<script src="{{ asset('js/penduduk/kartu_keluarga.js') }}"></script>

</body>
</html>