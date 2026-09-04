<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aceh Data Warehouse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body style="background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <div class="d-flex" style="min-height: 100vh;">

        <!-- ==================== SIDEBAR ==================== -->
        <div class="d-flex flex-column sidebar"
            style="width: 260px; background-color: #ffffff; border-right: 1px solid #e0e0e0; position: fixed; top: 0; left: 0; bottom: 0; z-index: 1000;">

            <!-- Logo Section -->
            <div class="d-flex align-items-center p-3" style="border-bottom: 1px solid #e8e8e8;">
                <div class="d-flex align-items-center justify-content-center rounded-circle"
                    style="width: 45px; height: 45px; background-color: #f0f0f0; border: 2px solid #d0d0d0; margin-right: 12px; flex-shrink: 0;">
                    <span style="font-weight: 800; font-size: 18px; color: #1a1a2e;">A</span>
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 16px; color: #1a1a2e; line-height: 1.2;">Aceh
                        Data<br>Warehouse</div>
                    <div style="font-size: 12px; color: #888;">Provinsi Aceh</div>
                </div>
            </div>

            <!-- Navigation Menu -->
            <div class="flex-grow-1 p-3" style="overflow-y: auto;">

                <!-- Penduduk -->
                <div class="mb-1 sidebar-menu-item">
                    <a href="#" class="d-flex align-items-center justify-content-between text-decoration-none p-2 rounded" style="color: #333;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-people-fill me-2" style="font-size: 18px;"></i>
                            <span style="font-weight: 600; font-size: 14px;">Penduduk</span>
                        </div>
                        <i class="bi bi-chevron-right chevron-icon" style="font-size: 14px;"></i>
                    </a>
                    <!-- Sub Menu Penduduk -->
                    <div class="ms-4 mt-1 submenu" style="max-height: 0px; opacity: 0; overflow: hidden; transition: max-height 0.3s ease, opacity 0.3s ease, padding 0.3s ease;">
                        <a href="{{ route('penduduk.jumlah_penduduk') }}" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Jumlah Penduduk</a>
                        <a href="{{ route('penduduk.kartu_keluarga') }}" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Kartu Keluarga</a>
                        <a href="#" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Pertumbuhan Penduduk</a>
                    </div>
                </div>

                <!-- Sosial -->
                <div class="mb-1 sidebar-menu-item">
                    <a href="#" class="d-flex align-items-center justify-content-between text-decoration-none p-2 rounded" style="color: #333;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-people me-2" style="font-size: 18px; color: #555;"></i>
                            <span style="font-weight: 500; font-size: 14px;">Sosial</span>
                        </div>
                    </a>
                </div>

                <!-- Kesehatan -->
                <div class="mb-1 sidebar-menu-item">
                    <a href="#" class="d-flex align-items-center justify-content-between text-decoration-none p-2 rounded" style="color: #333;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-hospital me-2" style="font-size: 18px; color: #555;"></i>
                            <span style="font-weight: 500; font-size: 14px;">Kesehatan</span>
                        </div>
                        <i class="bi bi-chevron-right chevron-icon" style="font-size: 14px; color: #999;"></i>
                    </a>
                    <!-- Sub Menu Kesehatan -->
                    <div class="ms-4 mt-1 submenu" style="max-height: 0px; opacity: 0; overflow: hidden; transition: max-height 0.3s ease, opacity 0.3s ease, padding 0.3s ease;">
                        <a href="{{ url('/kesehatan/puskesmas') }}" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Jumlah Puskesmas</a>
                        <a href="#" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Tenaga Medis</a>
                        <a href="#" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Program Prioritas</a>
                    </div>
                </div>

                <!-- Pendidikan -->
                <div class="mb-1 sidebar-menu-item">
                    <a href="#" class="d-flex align-items-center justify-content-between text-decoration-none p-2 rounded" style="color: #333;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-mortarboard me-2" style="font-size: 18px; color: #555;"></i>
                            <span style="font-weight: 500; font-size: 14px;">Pendidikan</span>
                        </div>
                    </a>
                </div>

            </div>
        </div>

        <!-- ==================== MAIN CONTENT ==================== -->
        <div class="flex-grow-1 main-content" style="margin-left: 260px;">

            <!-- Top Navbar -->
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="background-color: #ffffff; border-bottom: 1px solid #e0e0e0;">
                <h4 class="mb-0" style="font-weight: 800; color: #1a1a2e; font-size: 22px;">Sistem Informasi Data Nasional</h4>
                <div class="d-flex align-items-center gap-4">
                    <a href="#" class="text-decoration-none top-nav-link" style="font-weight: 700; color: #1a1a2e; font-size: 14px; border-bottom: 2px solid #1a1a2e; padding-bottom: 2px;">Dashboard</a>
                    <a href="#" class="text-decoration-none top-nav-link" style="font-weight: 500; color: #666; font-size: 14px;">Laporan</a>
                    <a href="#" class="text-decoration-none top-nav-link" style="font-weight: 500; color: #666; font-size: 14px;">Arsip</a>

                    <div class="d-flex align-items-center gap-3 ms-3">
                        <i class="bi bi-gear nav-icon" style="font-size: 18px; color: #555; cursor: pointer;"></i>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-4">

                <!-- Hero Section -->
                <div class="rounded-3 p-5 mb-4 hero-section" style="background: linear-gradient(135deg, #f0f4ff 0%, #f8f9ff 50%, #ffffff 100%); border: 1px solid #e0e4f0;">
                    <div class="row align-items-center">
                        <div class="col-lg-7">

                            <!-- Badge -->
                            <div class="d-inline-flex align-items-center rounded-pill px-3 py-1 mb-4" style="background-color: #e6f7f0; border: 1px solid #b2dfdb;">
                                <span class="rounded-circle d-inline-block me-2 graphic-dot" style="width: 10px; height: 10px; background-color: #0d9488;"></span>
                                <span style="font-size: 13px; font-weight: 600; color: #0d9488;">Pusat Data Terintegrasi</span>
                            </div>

                            <!-- Heading -->
                            <h1 class="mb-3" style="font-weight: 800; font-size: 42px; color: #1a1a2e; line-height: 1.2;">
                                Membangun Aceh<br>
                                Berbasis <span style="color: #0d9488;">Data Presisi</span>
                            </h1>

                            <!-- Description -->
                            <p class="mb-4" style="font-size: 15px; color: #555; line-height: 1.7; max-width: 580px;">
                                Platform analitik terpusat yang menyajikan data komprehensif terkait kependudukan,
                                sosial, kesehatan, dan pendidikan di Provinsi Aceh guna mendukung perumusan
                                kebijakan yang akurat dan transparan.
                            </p>

                            <!-- Buttons -->
                            <div class="d-flex gap-3">
                                <a href="#" class="btn btn-cta text-decoration-none px-4 py-2" style="background-color: #1a1a2e; color: #ffffff; font-weight: 600; font-size: 14px; border-radius: 6px;">
                                    Jelajahi Data <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                                <a href="#" class="btn btn-cta text-decoration-none px-4 py-2" style="background-color: #ffffff; color: #1a1a2e; font-weight: 600; font-size: 14px; border: 1px solid #d0d0d0; border-radius: 6px;">
                                    <i class="bi bi-download me-1"></i> Unduh Laporan Tahunan
                                </a>
                            </div>

                        </div>

                        <!-- Decorative Graphic -->
                        <div class="col-lg-5 d-flex justify-content-center">
                            <div class="position-relative" style="width: 280px; height: 280px;">
                                <!-- Background square -->
                                <div class="rounded-3" style="width: 280px; height: 280px; background-color: #e8f0f5; border: 1px solid #d0dce5; position: relative; overflow: hidden;">
                                    <!-- Circle -->
                                    <div class="rounded-circle" style="width: 200px; height: 200px; border: 1px solid #c8d8e0; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    </div>
                                    <!-- Diagonal lines -->
                                    <div style="position: absolute; top: 30%; left: 10%; width: 80%; height: 1px; background-color: #b0c4d0; transform: rotate(15deg);">
                                    </div>
                                    <div style="position: absolute; top: 60%; left: 10%; width: 80%; height: 1px; background-color: #0d9488; transform: rotate(-20deg); opacity: 0.5;">
                                    </div>
                                    <!-- Dots -->
                                    <div class="rounded-circle graphic-dot" style="width: 6px; height: 6px; background-color: #0d9488; position: absolute; top: 15%; left: 20%; opacity: 0.6;"></div>
                                    <div class="rounded-circle graphic-dot" style="width: 6px; height: 6px; background-color: #0d9488; position: absolute; top: 15%; left: 50%; opacity: 0.6;"></div>
                                    <div class="rounded-circle graphic-dot" style="width: 6px; height: 6px; background-color: #0d9488; position: absolute; top: 15%; left: 80%; opacity: 0.6;"></div>
                                    <div class="rounded-circle graphic-dot" style="width: 6px; height: 6px; background-color: #0d9488; position: absolute; top: 40%; left: 15%; opacity: 0.6;"></div>
                                    <div class="rounded-circle graphic-dot" style="width: 6px; height: 6px; background-color: #0d9488; position: absolute; top: 40%; left: 45%; opacity: 0.6;"></div>
                                    <div class="rounded-circle graphic-dot" style="width: 6px; height: 6px; background-color: #0d9488; position: absolute; top: 40%; left: 75%; opacity: 0.6;"></div>
                                    <div class="rounded-circle graphic-dot" style="width: 6px; height: 6px; background-color: #0d9488; position: absolute; top: 70%; left: 25%; opacity: 0.6;"></div>
                                    <div class="rounded-circle graphic-dot" style="width: 6px; height: 6px; background-color: #0d9488; position: absolute; top: 70%; left: 55%; opacity: 0.6;"></div>
                                    <div class="rounded-circle graphic-dot" style="width: 6px; height: 6px; background-color: #0d9488; position: absolute; top: 70%; left: 85%; opacity: 0.6;"></div>
                                    <div class="rounded-circle graphic-dot" style="width: 6px; height: 6px; background-color: #0d9488; position: absolute; top: 85%; left: 35%; opacity: 0.6;"></div>
                                    <div class="rounded-circle graphic-dot" style="width: 6px; height: 6px; background-color: #0d9488; position: absolute; top: 85%; left: 65%; opacity: 0.6;"></div>
                                    <div class="rounded-circle graphic-dot" style="width: 6px; height: 6px; background-color: #0d9488; position: absolute; top: 85%; left: 90%; opacity: 0.6;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== SEKTOR UTAMA ==================== -->
                <div class="mb-4">
                    <h3 class="mb-3" style="font-weight: 800; color: #1a1a2e; font-size: 24px;">Sektor Utama</h3>
                    <hr style="border-color: #e0e0e0; margin: 0 0 20px 0;">

                    <div class="row g-4">

                        <!-- Card: Penduduk -->
                        <div class="col-lg-3 col-md-6 sektor-card">
                            <div class="card h-100" style="border: 1px solid #d8dde8; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                                <div class="card-body p-4 d-flex flex-column">
                                    <!-- Icon Box -->
                                    <div class="d-flex align-items-center justify-content-center rounded-3 mb-4" style="width: 52px; height: 52px; background-color: #0f1b2d; border-radius: 8px;">
                                        <i class="bi bi-person-fill" style="color: #ffffff; font-size: 22px;"></i>
                                    </div>
                                    <!-- Title -->
                                    <h5 class="mb-2" style="font-weight: 800; color: #1a1a2e; font-size: 20px;">Penduduk</h5>
                                    <!-- Description -->
                                    <p class="mb-3 flex-grow-1" style="font-size: 14px; color: #5a6577; line-height: 1.6;">
                                        Data demografi, sebaran penduduk, angka kelahiran, dan kematian per kabupaten/kota.
                                    </p>
                                    <hr style="border-color: #e0e4f0; margin: 0 0 16px 0;">
                                    <!-- Link Akses Modul -->
                                    <a href="#" class="d-flex align-items-center justify-content-between text-decoration-none" style="color: #0d9488; font-weight: 700; font-size: 14px;">
                                        <span>Akses Modul</span>
                                        <i class="bi bi-arrow-right" style="font-size: 18px;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Sosial -->
                        <div class="col-lg-3 col-md-6 sektor-card">
                            <div class="card h-100" style="border: 1px solid #d8dde8; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                                <div class="card-body p-4 d-flex flex-column">
                                    <!-- Icon Box -->
                                    <div class="d-flex align-items-center justify-content-center rounded-3 mb-4" style="width: 52px; height: 52px; background-color: #0f1b2d; border-radius: 8px;">
                                        <i class="bi bi-people-fill" style="color: #ffffff; font-size: 22px;"></i>
                                    </div>
                                    <!-- Title -->
                                    <h5 class="mb-2" style="font-weight: 800; color: #1a1a2e; font-size: 20px;">Sosial</h5>
                                    <!-- Description -->
                                    <p class="mb-3 flex-grow-1" style="font-size: 14px; color: #5a6577; line-height: 1.6;">
                                        Indikator kesejahteraan, tingkat kemiskinan, ketenagakerjaan, dan program bantuan sosial.
                                    </p>
                                    <hr style="border-color: #e0e4f0; margin: 0 0 16px 0;">
                                    <!-- Link Akses Modul -->
                                    <a href="#" class="d-flex align-items-center justify-content-between text-decoration-none" style="color: #0d9488; font-weight: 700; font-size: 14px;">
                                        <span>Akses Modul</span>
                                        <i class="bi bi-arrow-right" style="font-size: 18px;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Kesehatan -->
                        <div class="col-lg-3 col-md-6 sektor-card">
                            <div class="card h-100" style="border: 1px solid #d8dde8; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                                <div class="card-body p-4 d-flex flex-column">
                                    <!-- Icon Box -->
                                    <div class="d-flex align-items-center justify-content-center rounded-3 mb-4" style="width: 52px; height: 52px; background-color: #0f1b2d; border-radius: 8px;">
                                        <i class="bi bi-plus-lg" style="color: #ffffff; font-size: 22px;"></i>
                                    </div>
                                    <!-- Title -->
                                    <h5 class="mb-2" style="font-weight: 800; color: #1a1a2e; font-size: 20px;">Kesehatan</h5>
                                    <!-- Description -->
                                    <p class="mb-3 flex-grow-1" style="font-size: 14px; color: #5a6577; line-height: 1.6;">
                                        Fasilitas pelayanan, gizi masyarakat, angka harapan hidup, dan pengendalian penyakit menular.
                                    </p>
                                    <hr style="border-color: #e0e4f0; margin: 0 0 16px 0;">
                                    <!-- Link Akses Modul -->
                                    <a href="{{ url('/kesehatan/puskesmas') }}" class="d-flex align-items-center justify-content-between text-decoration-none" style="color: #0d9488; font-weight: 700; font-size: 14px;">
                                        <span>Akses Modul</span>
                                        <i class="bi bi-arrow-right" style="font-size: 18px;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Pendidikan -->
                        <div class="col-lg-3 col-md-6 sektor-card">
                            <div class="card h-100" style="border: 1px solid #d8dde8; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                                <div class="card-body p-4 d-flex flex-column">
                                    <!-- Icon Box -->
                                    <div class="d-flex align-items-center justify-content-center rounded-3 mb-4" style="width: 52px; height: 52px; background-color: #0f1b2d; border-radius: 8px;">
                                        <i class="bi bi-diamond" style="color: #ffffff; font-size: 22px;"></i>
                                    </div>
                                    <!-- Title -->
                                    <h5 class="mb-2" style="font-weight: 800; color: #1a1a2e; font-size: 20px;">Pendidikan</h5>
                                    <!-- Description -->
                                    <p class="mb-3 flex-grow-1" style="font-size: 14px; color: #5a6577; line-height: 1.6;">
                                        Angka partisipasi sekolah, rasio guru-murid, fasilitas pendidikan, dan indeks literasi.
                                    </p>
                                    <hr style="border-color: #e0e4f0; margin: 0 0 16px 0;">
                                    <!-- Link Akses Modul -->
                                    <a href="#" class="d-flex align-items-center justify-content-between text-decoration-none" style="color: #0d9488; font-weight: 700; font-size: 14px;">
                                        <span>Akses Modul</span>
                                        <i class="bi bi-arrow-right" style="font-size: 18px;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ==================== FOOTER ==================== -->
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/landing.js') }}"></script>
</body>

</html>