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
</head>
<body style="background-color: #f8f9fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

<div class="d-flex" style="min-height: 100vh;">

    <!-- ==================== SIDEBAR ==================== -->
    <div class="d-flex flex-column" style="width: 260px; background-color: #ffffff; border-right: 1px solid #e0e0e0; position: fixed; top: 0; left: 0; bottom: 0; z-index: 1000;">

        <!-- Logo Section -->
        <div class="d-flex align-items-center p-3" style="border-bottom: 1px solid #e8e8e8;">
            <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 45px; height: 45px; background-color: #0d9488; margin-right: 12px; flex-shrink: 0;">
                <i class="bi bi-building" style="color: #ffffff; font-size: 22px;"></i>
            </div>
            <div>
                <div style="font-weight: 800; font-size: 16px; color: #1a1a2e; line-height: 1.2;">Aceh Data<br>Warehouse</div>
                <div style="font-size: 12px; color: #888;">Sistem Integrasi Daerah</div>
            </div>
        </div>

        <!-- Navigation Menu -->
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
                        <a href="{{ route('penduduk.jumlah_penduduk') }}" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Jumlah Penduduk</a>
                        <a href="{{ route('penduduk.kartu_keluarga') }}" class="d-block text-decoration-none py-1 px-2 rounded" style="background-color: #e8f5f0; color: #0d9488; font-weight: 600; font-size: 13px;">Kartu Keluarga</a>
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

        <!-- Footer Sidebar -->
        <div class="p-3" style="border-top: 1px solid #e8e8e8;">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-2" style="width: 36px; height: 36px; background-color: #0d9488;">
                    <i class="bi bi-gear-fill" style="color: #ffffff; font-size: 18px;"></i>
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 13px; color: #1a1a2e;">Diskominfo Aceh</div>
                    <div style="font-size: 11px; color: #888;">Pemerintah Provinsi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="flex-grow-1" style="margin-left: 260px;">

        <!-- Header -->
        <header class="px-4 py-3" style="background-color: #ffffff; border-bottom: 1px solid #e0e4f0;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="mb-1" style="font-weight: 800; color: #1a1a2e; font-size: 24px;">Analisis Data Kartu Keluarga (KK) Provinsi Aceh</h1>
                    <p class="mb-0" style="font-size: 13px; color: #8892a4;">Cakupan data: 2021–2023, 23 kabupaten/kota.</p>
                </div>
                <div>
                    <select id="filter-tahun" class="form-select" style="width: 150px; border: 1px solid #d0d8e0; border-radius: 6px; font-size: 14px;">
                        <option value="2024" selected>2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
                        <option value="2020">2020</option>                    </select>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="p-4">

            <!-- ==================== SUMMARY CARDS ==================== -->
            <div class="row g-4 mb-4">

                <!-- Card 1: Total Kartu Keluarga -->
                <div class="col-md-4">
                    <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="text-uppercase" style="font-size: 12px; font-weight: 700; color: #5a6577; letter-spacing: 1px;">Total Kartu Keluarga</span>
                                <span class="badge rounded-pill" style="background-color: #e8f0ff; color: #1a1a2e; font-size: 11px; font-weight: 600; padding: 4px 10px;">Aceh 2024</span>
                            </div>
                            <div class="mb-3">
                                <strong id="stat-total-kk" style="font-size: 38px; font-weight: 800; color: #1a1a2e; letter-spacing: -1px;">1.412.850</strong>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-3" style="border-top: 1px solid #e8e8e8;">
                                <span class="badge rounded-pill" style="background-color: #e8f5f0; color: #0d9488; font-size: 13px; font-weight: 700; padding: 6px 12px;">
                                    <i class="bi bi-arrow-up-short me-1" style="font-size: 16px;"></i> +3,8%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: KK Tertinggi -->
                <div class="col-md-4">
                    <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="text-uppercase" style="font-size: 12px; font-weight: 700; color: #5a6577; letter-spacing: 1px;">KK Tertinggi</span>
                                <span class="badge rounded-pill" style="background-color: #e8f0ff; color: #1a1a2e; font-size: 11px; font-weight: 600; padding: 4px 10px;">Kabupaten</span>
                            </div>
                            <div class="mb-3">
                                <strong style="font-size: 24px; font-weight: 800; color: #1a1a2e;">Kab. Aceh Utara</strong>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-3" style="border-top: 1px solid #e8e8e8;">
                                <span class="badge rounded-pill" style="background-color: #e8f5f0; color: #0d9488; font-size: 13px; font-weight: 700; padding: 6px 12px;">164.200 KK</span>
                                <span style="font-size: 13px; color: #8892a4;">11,6% dari total Aceh</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Pertumbuhan Tercepat -->
                <div class="col-md-4">
                    <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="text-uppercase" style="font-size: 12px; font-weight: 700; color: #5a6577; letter-spacing: 1px;">Pertumbuhan Tercepat</span>
                                <span class="badge rounded-pill" style="background-color: #e8f0ff; color: #1a1a2e; font-size: 11px; font-weight: 600; padding: 4px 10px;">Kota</span>
                            </div>
                            <div class="mb-3">
                                <strong style="font-size: 24px; font-weight: 800; color: #1a1a2e;">Kota Banda Aceh</strong>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-3" style="border-top: 1px solid #e8e8e8;">
                                <span class="badge rounded-pill" style="background-color: #e8f5f0; color: #0d9488; font-size: 13px; font-weight: 700; padding: 6px 12px;">+5,5% Tren</span>
                                <span style="font-size: 13px; color: #8892a4;">Total: 69.850 KK</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== CHARTS SECTION ==================== -->
            <div class="row g-4 mb-4">

                <!-- Chart 1: Tren Pertumbuhan KK -->
                <div class="col-lg-7">
                    <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <h2 class="mb-0" style="font-weight: 800; color: #1a1a2e; font-size: 20px;">Tren Pertumbuhan KK Provinsi Aceh</h2>
                                <span class="d-inline-flex align-items-center rounded-pill px-3 py-1" style="background-color: #e8f5f0; border: 1px solid #b2dfdb;">
                                    <span class="rounded-circle d-inline-block me-2" style="width: 8px; height: 8px; background-color: #0d9488;"></span>
                                    <span style="font-size: 12px; font-weight: 700; color: #0d9488;">Tren Positif</span>
                                </span>
                            </div>
                            <p class="mb-4" style="font-size: 13px; color: #8892a4;">Evolusi akumulasi jumlah Kepala Keluarga periode 2020 – 2024</p>

                            <div style="height: 320px;">
                                <canvas id="trenKKChart"></canvas>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-4 pt-3" style="border-top: 1px solid #e8e8e8;">
                                <div style="font-size: 13px; color: #5a6577;">
                                    Rata-rata kenaikan tahunan: <strong style="color: #1a1a2e;">~33.000 KK / tahun</strong>
                                </div>
                                <div style="font-size: 13px; color: #8892a4;">
                                    Sumber: Dinas Registrasi Kependudukan Aceh
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart 2: Distribusi Wilayah -->
                <div class="col-lg-5">
                    <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <h2 class="mb-0" style="font-weight: 800; color: #1a1a2e; font-size: 20px;">Distribusi Wilayah 2024</h2>
                                <span class="badge rounded-pill" style="background-color: #e8f0ff; color: #1a1a2e; font-size: 11px; font-weight: 600; padding: 4px 10px;">Top Daerah</span>
                            </div>
                            <p class="mb-4" style="font-size: 13px; color: #8892a4;">Perbandingan jumlah KK di daerah berpopulasi terbesar</p>

                            <!-- Horizontal Bar Chart -->
                            <div class="mb-4">
                                <!-- Item 1 -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span style="font-size: 13px; font-weight: 600; color: #1a1a2e;">Kab. Aceh Utara</span>
                                        <span style="font-size: 13px; font-weight: 700; color: #1a1a2e;">164.200 KK</span>
                                    </div>
                                    <div class="progress" style="height: 10px; background-color: #e8f0ff; border-radius: 5px;">
                                        <div class="progress-bar" style="width: 100%; background: linear-gradient(90deg, #0d9488 0%, #14b8a6 100%); border-radius: 5px;"></div>
                                    </div>
                                </div>

                                <!-- Item 2 -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span style="font-size: 13px; font-weight: 600; color: #1a1a2e;">Kab. Pidie</span>
                                        <span style="font-size: 13px; font-weight: 700; color: #1a1a2e;">129.800 KK</span>
                                    </div>
                                    <div class="progress" style="height: 10px; background-color: #e8f0ff; border-radius: 5px;">
                                        <div class="progress-bar" style="width: 79%; background: linear-gradient(90deg, #0d9488 0%, #14b8a6 100%); border-radius: 5px;"></div>
                                    </div>
                                </div>

                                <!-- Item 3 -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span style="font-size: 13px; font-weight: 600; color: #1a1a2e;">Kab. Bireuen</span>
                                        <span style="font-size: 13px; font-weight: 700; color: #1a1a2e;">116.450 KK</span>
                                    </div>
                                    <div class="progress" style="height: 10px; background-color: #e8f0ff; border-radius: 5px;">
                                        <div class="progress-bar" style="width: 71%; background: linear-gradient(90deg, #0d9488 0%, #14b8a6 100%); border-radius: 5px;"></div>
                                    </div>
                                </div>

                                <!-- Item 4 -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span style="font-size: 13px; font-weight: 600; color: #1a1a2e;">Kab. Aceh Timur</span>
                                        <span style="font-size: 13px; font-weight: 700; color: #1a1a2e;">111.300 KK</span>
                                    </div>
                                    <div class="progress" style="height: 10px; background-color: #e8f0ff; border-radius: 5px;">
                                        <div class="progress-bar" style="width: 68%; background: linear-gradient(90deg, #0d9488 0%, #14b8a6 100%); border-radius: 5px;"></div>
                                    </div>
                                </div>

                                <!-- Item 5 -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span style="font-size: 13px; font-weight: 600; color: #1a1a2e;">Kab. Aceh Besar</span>
                                        <span style="font-size: 13px; font-weight: 700; color: #1a1a2e;">105.600 KK</span>
                                    </div>
                                    <div class="progress" style="height: 10px; background-color: #e8f0ff; border-radius: 5px;">
                                        <div class="progress-bar" style="width: 64%; background: linear-gradient(90deg, #0d9488 0%, #14b8a6 100%); border-radius: 5px;"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Proporsi Kabupaten vs Perkotaan -->
                            <div class="pt-3" style="border-top: 1px solid #e8e8e8;">
                                <div class="d-flex justify-content-between mb-2">
                                    <span style="font-size: 13px; font-weight: 700; color: #1a1a2e;">Proporsi: Kabupaten vs Perkotaan</span>
                                    <span style="font-size: 13px; color: #8892a4;">Total 23 Daerah</span>
                                </div>
                                <div class="progress mb-3" style="height: 12px; border-radius: 6px; overflow: hidden;">
                                    <div class="progress-bar" style="width: 78%; background-color: #0d9488;"></div>
                                    <div class="progress-bar" style="width: 22%; background-color: #14b8a6;"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span class="rounded-circle d-inline-block me-2" style="width: 10px; height: 10px; background-color: #0d9488;"></span>
                                        <span style="font-size: 12px; color: #5a6577;">18 Kabupaten (78%)</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="rounded-circle d-inline-block me-2" style="width: 10px; height: 10px; background-color: #14b8a6;"></span>
                                        <span style="font-size: 12px; color: #5a6577;">5 Kota (22%)</span>
                                    </div>
                                </div>
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
                        <p class="mb-0" style="font-size: 13px; color: #8892a4;">Data agregat berkala tahun 2020, 2022, hingga semester terkini 2024</p>
                    </div>
                    <div class="position-relative" style="width: 280px;">
                        <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #8892a4;"></i>
                        <input type="search" class="form-control ps-5" placeholder="Cari nama Kab/Kota..." style="border-radius: 6px; border: 1px solid #d0d8e0; font-size: 14px;">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size: 14px;">
                            <thead style="background-color: #eef2f9;">
                                <tr>
                                    <th class="px-4 py-3" style="font-weight: 700; color: #1a1a2e; border-bottom: 1px solid #d8dde8; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Kabupaten / Kota</th>
                                    <th class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e; border-bottom: 1px solid #d8dde8; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">KK 2020</th>
                                    <th class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e; border-bottom: 1px solid #d8dde8; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">KK 2022</th>
                                    <th class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e; border-bottom: 1px solid #d8dde8; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">KK 2024</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-building-fill me-2" style="color: #0d9488;"></i>
                                        <strong style="color: #1a1a2e;">Kota Banda Aceh</strong>
                                    </td>
                                    <td class="px-4 py-3 text-end">63.450</td>
                                    <td class="px-4 py-3 text-end">66.200</td>
                                    <td class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e;">69.850</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-building me-2" style="color: #5a6577;"></i>
                                        <strong style="color: #1a1a2e;">Kab. Aceh Utara</strong>
                                    </td>
                                    <td class="px-4 py-3 text-end">151.200</td>
                                    <td class="px-4 py-3 text-end">157.900</td>
                                    <td class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e;">164.200</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-building me-2" style="color: #5a6577;"></i>
                                        <strong style="color: #1a1a2e;">Kab. Pidie</strong>
                                    </td>
                                    <td class="px-4 py-3 text-end">119.500</td>
                                    <td class="px-4 py-3 text-end">124.600</td>
                                    <td class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e;">129.800</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-building me-2" style="color: #5a6577;"></i>
                                        <strong style="color: #1a1a2e;">Kab. Bireuen</strong>
                                    </td>
                                    <td class="px-4 py-3 text-end">106.800</td>
                                    <td class="px-4 py-3 text-end">111.500</td>
                                    <td class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e;">116.450</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-building me-2" style="color: #5a6577;"></i>
                                        <strong style="color: #1a1a2e;">Kab. Aceh Timur</strong>
                                    </td>
                                    <td class="px-4 py-3 text-end">101.900</td>
                                    <td class="px-4 py-3 text-end">106.400</td>
                                    <td class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e;">111.300</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-building me-2" style="color: #5a6577;"></i>
                                        <strong style="color: #1a1a2e;">Kab. Aceh Besar</strong>
                                    </td>
                                    <td class="px-4 py-3 text-end">96.700</td>
                                    <td class="px-4 py-3 text-end">101.100</td>
                                    <td class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e;">105.600</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-building me-2" style="color: #5a6577;"></i>
                                        <strong style="color: #1a1a2e;">Kab. Aceh Barat</strong>
                                    </td>
                                    <td class="px-4 py-3 text-end">54.300</td>
                                    <td class="px-4 py-3 text-end">57.100</td>
                                    <td class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e;">59.800</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-building-fill me-2" style="color: #0d9488;"></i>
                                        <strong style="color: #1a1a2e;">Kota Lhokseumawe</strong>
                                    </td>
                                    <td class="px-4 py-3 text-end">45.800</td>
                                    <td class="px-4 py-3 text-end">48.200</td>
                                    <td class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e;">51.100</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-building me-2" style="color: #5a6577;"></i>
                                        <strong style="color: #1a1a2e;">Kab. Pidie Jaya</strong>
                                    </td>
                                    <td class="px-4 py-3 text-end">39.100</td>
                                    <td class="px-4 py-3 text-end">41.000</td>
                                    <td class="px-4 py-3 text-end" style="font-weight: 700; color: #1a1a2e;">43.200</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer border-0 d-flex align-items-center justify-content-between p-4" style="background-color: #ffffff; border-radius: 0 0 12px 12px;">
                    <div style="font-size: 13px; color: #5a6577;">
                        Menampilkan <strong style="color: #1a1a2e;">9</strong> dari <strong style="color: #1a1a2e;">23</strong> Daerah Kabupaten/Kota
                    </div>
                    <nav aria-label="Pagination">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" style="border: none; color: #8892a4;">&lt;</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#" style="background-color: #1a1a2e; border-color: #1a1a2e; color: #ffffff; font-weight: 600;">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#" style="border: none; color: #5a6577;">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#" style="border: none; color: #5a6577;">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#" style="border: none; color: #5a6577;">&gt;</a>
                            </li>
                        </ul>
                    </nav>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JS untuk Kartu Keluarga -->
<script src="{{ asset('js/penduduk/kartu_keluarga.js') }}"></script>

</body>
</html>