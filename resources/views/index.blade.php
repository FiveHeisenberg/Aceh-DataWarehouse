<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aceh Data Warehouse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .profile-toggle::after {
            display: none;
        }

        .profile-toggle:focus {
            box-shadow: none;
        }

        .profile-menu {
            min-width: 210px;
            border: 1px solid #e0e4f0;
            border-radius: 10px
            padding: 6px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.10);
        }

        .profile-menu .dropdown-item {
            font-size: 13px;
            color: #333;
            border-radius: 6px;
            padding: 8px 12px;
        }

        .profile-menu .dropdown-item:hover {
            background-color: #e8f5f0;
            color: #0d9488;
        }

        .modal { z-index: 1200; }
        .modal-backdrop { z-index: 1190; }

        /* NOTIFIKASI "LENGKAPI DATA DIRI */
        .toast-container-adw {
            position: fixed;
            top: 86px;
            right: 20px;
            z-index: 1200;
        }

        .toast-adw {
            opacity: 0;
            transition: opacity .35s ease-in-out;
            background-color: #fef3c7;
            border-color: #fcd34d;
        }

        .toast-adw.show {
            opacity: 1;
        }

        .toast-adw .toast-header {
            background-color: #fef3c7;
            border-bottom-color: #fcd34d;
        }

        /* ANIMASI FADE-OUT NOTIFIKASI */
        .toast-adw:not(.show) {
            display: block;
            pointer-events: none;
        }

        @media (max-width: 575.98px) {
            .toast-container-adw { left: 20px; right: 20px; width: auto; }
        }

    </style>
</head>

<body style="background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    <div class="d-flex flex-column" style="min-height: 100vh;">

        <!-- ==================== TOP NAVBAR ==================== -->
        <nav class="d-flex align-items-center px-4" style="height: 70px; background-color: #ffffff; border-bottom: 1px solid #e0e4f0; position: fixed; top: 0; left: 0; right: 0; z-index: 1100;">

            <!-- Logo -->
            <div class="d-flex align-items-center" style="width: 300px; flex-shrink: 0;">
                <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background-color: #f0f0f0; border: 2px solid #d0d0d0; margin-right: 10px; flex-shrink: 0;">
                    <span style="font-weight: 800; font-size: 16px; color: #1a1a2e;">A</span>
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 14px; color: #1a1a2e; line-height: 1.2;">Aceh Data<br>Warehouse</div>
                </div>
            </div>

            <!-- Back + Top Nav -->
            <div class="d-flex align-items-center">
                <a href="/" class="text-decoration-none me-4" style="color: #1a1a2e; font-weight: 600; font-size: 15px;">Home</a>
                <a href="#" class="text-decoration-none" style="color: #5a6577; font-weight: 500; font-size: 15px;">About</a>
            </div>

            <!-- Spacer -->
            <div class="flex-grow-1"></div>

            <!-- Icons -->
            <div class="d-flex align-items-center" style="gap: 18px;">
                <i class="bi bi-bell nav-icon" style="font-size: 19px; color: #5a6577;"></i>
                <i class="bi bi-gear nav-icon" style="font-size: 19px; color: #5a6577;"></i>
                <div class="dropdown">
                    <button class="btn p-0 border-0 bg-transparent dropdown-toggle profile-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu akun">
                        <span class="d-flex align-items-center justify-content-center rounded-circle" style="width: 34px; height: 34px; background-color: #eef2f9;">
                            <i class="bi bi-person-fill" style="font-size: 18px; color: #5a6577;"></i>
                        </span>
                    </button>

                    @php($user = auth()->user())
                    @php($adminRoleId = 1)
                    <ul class="dropdown-menu dropdown-menu-end profile-menu">
                        <li class="px-3 pt-1 pb-2">
                            <div style="font-size: 13px; font-weight: 700; color: #1a1a2e;">{{ $user->nama_lengkap ?? $user->username ?? 'Administrator' }}</div>
                            <div style="font-size: 11px; color: #8892a4;">{{ $user?->email ?: '-' }}</div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>

                        <li>
                            <a href="{{ route('profile') }}" class="dropdown-item d-flex align-items-center">
                                <i class="bi bi-person me-2"></i> Profile
                            </a>
                        </li>

                        @if($user->id_role === $adminRoleId)
                        <li>
                            <a href="{{ route('manage-user') }}" class="dropdown-item d-flex align-items-center">
                                <i class="bi bi-people me-2"></i> Manajemen User
                            </a>
                        </li>
                        @endif

                        <!-- ETL - hanya untuk admin -->
                        @if($user->id_role === $adminRoleId)
                        <li>
                            <a href="http://192.168.222.152:8080/" target="blank" class="dropdown-item d-flex align-items-center">
                                <i class="bi bi-arrow-repeat me-2"></i> ETL
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        @endif

                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center w-100" style="color: #dc3545;" data-bs-toggle="modal" data-bs-target='#konfirmasiLogout'>
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="d-flex flex-grow-1" style="margin-top: 70px;">

            <!-- ==================== SIDEBAR ==================== -->
            <div class="p-3 sidebar" style="width: 260px; background-color: #ffffff; border-right: 1px solid #e0e0e0; position: fixed; top: 70px; left: 0; bottom: 0; overflow-y: auto; z-index: 1000;">

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
                        <a href="{{ route('penduduk.kartu_keluarga') }}" class="d-block text-decoration-none py-2 px-2" style="font-size: 13px; color: #555;">Kartu Keluarga</a>
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
            <div class="flex-grow-1 main-content" style="margin-left: 260px;">

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
                                    <a href="{{ route('penduduk.jumlah_penduduk')}}" class="d-flex align-items-center justify-content-between text-decoration-none" style="color: #0d9488; font-weight: 700; font-size: 14px;">
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
    </div>

    <!-- NOTIFIKASI KONFIRMASI LOGOUT -->
    <div class="modal fade" id="konfirmasiLogout" tabindex="-1" role="dialog" aria-labelledby="konfirmasiLogoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form id="formLogout" method="POST" action="{{ route('logout') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-body text-center p-4 pb-2">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 56px; height: 56px; background-color: #fdecec;">
                            <i class="bi bi-box-arrow-right" aria-hidden="true" style="font-size: 24px; color: #dc3545;"></i>
                        </div>
                        <h5 class="modal-title mb-2" id="konfirmasiLogoutLabel" style="font-weight: 700; font-size: 17px; color: #1a1a2e;">Keluar dari Akun?</h5>
                        <p class="mb-0" style="font-size: 13px; color: #5a6577; line-height: 1.6;"> Anda akan keluar dari sesi ini dan perlu masuk kembali untuk melanjutkan.</p>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4 gap-2">
                        <button type="button" class="btn btn-sm flex-fill" data-bs-dismiss="modal" style="background-color: #f0f2f5; color: #333; border: 1px solid #d0d0d0; font-weight: 600;">Batal</button>
                        <button type="submit" form="formLogout" class="btn btn-sm flex-fill" style="background-color: #dc3545; color: #ffffff; border: 1px solid #dc3545; font-weight: 600;">Ya, Logout</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- NOTIFIKASI "LENGKAPI DATA DIRI" -->
    @php($notifLengkapiDiri = session()->pull('notif_lengkapi_diri', false))

    @if ($notifLengkapiDiri)
    <div class="toast-container toast-container-adw">
        <div id="notifLengkapiDiri" class="toast toast-adw" role="alert" aria-live="polite" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-person-exclamation me-2" style="color: #b45309;"></i>
                <strong class="me-auto" style="color: #1a1a2e;">Data Diri Belum Lengkap</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Tutup"></button>
            </div>
            <div class="toast-body" style="color: #5a6577;">Silahkan Lengkapi data diri anda</div>
        </div>
    </div>
    @endif

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/landing.js') }}"></script>
    <script>
        // NOTIFIKASI LENGKAPI DATA DIRI
        const toastLengkapiDiri = document.getElementById('notifLengkapiDiri');

        if (toastLengkapiDiri) {
            bootstrap.Toast.getOrCreateInstance(toastLengkapiDiri, {
                delay: 2000,
                autohide: true
            }).show();
        }
    </script>
</body>

</html>