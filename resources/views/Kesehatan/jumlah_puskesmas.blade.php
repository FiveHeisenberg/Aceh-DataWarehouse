<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sebaran Fasilitas Puskesmas - Aceh Data Warehouse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { border: 1px solid #d8dde8; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff; }
        .text-tosca { color: #0d9488; }
        .bg-tosca-light { background-color: #e8f5f0; }
        .table-responsive::-webkit-scrollbar { height: 8px; }
        .table-responsive::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
    </style>
</head>

<body>

    <div class="d-flex" style="min-height: 100vh;">

        <!-- ==================== SIDEBAR ==================== -->
        <div class="d-flex flex-column sidebar" style="width: 260px; background-color: #ffffff; border-right: 1px solid #e0e0e0; position: fixed; top: 0; left: 0; bottom: 0; z-index: 1000;">
            <div class="d-flex align-items-center p-3" style="border-bottom: 1px solid #e8e8e8;">
                <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 45px; height: 45px; background-color: #f0f0f0; border: 2px solid #d0d0d0; margin-right: 12px; flex-shrink: 0;">
                    <span style="font-weight: 800; font-size: 18px; color: #1a1a2e;">A</span>
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 16px; color: #1a1a2e; line-height: 1.2;">Aceh Data<br>Warehouse</div>
                    <div style="font-size: 12px; color: #888;">Provinsi Aceh</div>
                </div>
            </div>

            <div class="flex-grow-1 p-3" style="overflow-y: auto;">
                
                <!-- Penduduk -->
                <div class="mb-1 sidebar-menu-item">
                    <a href="#" class="d-flex align-items-center justify-content-between text-decoration-none p-2 rounded" style="color: #333;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-people-fill me-2" style="font-size: 18px;"></i>
                            <span style="font-weight: 500; font-size: 14px;">Penduduk</span>
                        </div>
                        <i class="bi bi-chevron-right chevron-icon" style="font-size: 14px;"></i>
                    </a>
                    <div class="ms-4 mt-1 submenu" style="max-height: 0px; opacity: 0; overflow: hidden; transition: all 0.3s ease;">
                        <a href="{{ route('penduduk.jumlah_penduduk') ?? '#' }}" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Jumlah Penduduk</a>
                        <a href="{{ route('penduduk.kartu_keluarga') ?? '#' }}" class="d-block text-decoration-none py-1 px-2" style="font-size: 13px; color: #555;">Kartu Keluarga</a>
                    </div>
                </div>

                <!-- Kesehatan (ACTIVE STATE) -->
                <div class="mb-1 sidebar-menu-item">
                    <a href="#" class="d-flex align-items-center justify-content-between text-decoration-none p-2 rounded menu-toggle bg-tosca-light text-tosca">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-hospital me-2" style="font-size: 18px;"></i>
                            <span style="font-weight: 600; font-size: 14px;">Kesehatan</span>
                        </div>
                        <i class="bi bi-chevron-down chevron-icon" style="font-size: 14px;"></i>
                    </a>
                    <div class="ms-4 mt-1 submenu" style="max-height: 200px; opacity: 1; overflow: hidden; transition: all 0.3s ease;">
                        <a href="{{ url('/kesehatan/puskesmas') }}" class="d-block text-decoration-none py-1 px-2 mb-1" style="font-weight: 600; font-size: 13px; color: #0d9488; background-color: #e8f5f0; border-left: 3px solid #0d9488; padding-left: 5px;">Jumlah Puskesmas</a>
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
            <div class="d-flex align-items-center justify-content-between px-4 py-3 bg-white" style="border-bottom: 1px solid #e0e0e0;">
                <div>
                    <h4 class="mb-1" style="font-weight: 800; color: #1a1a2e; font-size: 20px;">Sebaran Fasilitas Puskesmas Aceh</h4>
                    <div style="font-size: 13px; color: #6c757d;">Cakupan data: Kapasitas dan rasio pemerataan di 23 kabupaten/kota.</div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <select class="form-select form-select-sm shadow-sm" style="width: 120px; border-color: #d8dde8; cursor: pointer;">
                        <option value="2024">Tahun 2024</option>
                        <option value="2023">Tahun 2023</option>
                    </select>
                    <button class="btn btn-sm text-white px-3 shadow-sm" style="background-color: #0d9488; font-weight: 500;">
                        <i class="bi bi-download me-1"></i> Ekspor
                    </button>
                </div>
            </div>

            <!-- Dashboard Content Area -->
            <div class="p-4">
                
                <!-- 4 KPI Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="card-custom h-100 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="font-size: 11px; font-weight: 700; color: #8892a4; letter-spacing: 0.5px;">TOTAL PUSKESMAS</span>
                                <i class="bi bi-hospital text-tosca" style="font-size: 18px;"></i>
                            </div>
                            <h3 class="mb-1" style="font-weight: 800; color: #1a1a2e;">361 <span style="font-size: 13px; font-weight: 500; color: #8892a4;">Unit</span></h3>
                            <p class="mb-0" style="font-size: 12px; color: #6c757d;">Tersebar di 23 Kab/Kota</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card-custom h-100 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="font-size: 11px; font-weight: 700; color: #8892a4; letter-spacing: 0.5px;">RAWAT INAP</span>
                                <i class="bi bi-bandaid text-tosca" style="font-size: 18px;"></i>
                            </div>
                            <h3 class="mb-1" style="font-weight: 800; color: #1a1a2e;">194 <span style="font-size: 13px; font-weight: 500; color: #8892a4;">Unit</span></h3>
                            <p class="mb-0" style="font-size: 12px; color: #6c757d;"><span class="text-tosca fw-bold">53,7%</span> dari seluruh faskes</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card-custom h-100 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="font-size: 11px; font-weight: 700; color: #8892a4; letter-spacing: 0.5px;">NON-RAWAT INAP</span>
                                <i class="bi bi-building text-secondary" style="font-size: 18px;"></i>
                            </div>
                            <h3 class="mb-1" style="font-weight: 800; color: #1a1a2e;">167 <span style="font-size: 13px; font-weight: 500; color: #8892a4;">Unit</span></h3>
                            <p class="mb-0" style="font-size: 12px; color: #6c757d;"><span class="text-dark fw-bold">46,3%</span> pelayanan promotif</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="card-custom h-100 p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span style="font-size: 11px; font-weight: 700; color: #8892a4; letter-spacing: 0.5px;">RASIO RATA-RATA</span>
                                <i class="bi bi-people text-tosca" style="font-size: 18px;"></i>
                            </div>
                            <h3 class="mb-1" style="font-weight: 800; color: #1a1a2e;">15.186 <span style="font-size: 13px; font-weight: 500; color: #8892a4;">Jiwa/Unit</span></h3>
                            <p class="mb-0" style="font-size: 12px; color: #0d9488; font-weight: 500;"><i class="bi bi-check-circle-fill me-1"></i>Kategori Aman</p>
                        </div>
                    </div>
                </div>

                <!-- Main Data Row -->
                <div class="row g-4">
                    
                    <!-- Tabel Data Master -->
                    <div class="col-lg-8">
                        <div class="card-custom h-100">
                            <div class="d-flex justify-content-between align-items-center p-4 border-bottom border-light">
                                <div>
                                    <h5 class="mb-1" style="font-weight: 700; color: #1a1a2e; font-size: 16px;">Data Master Fasilitas Kesehatan</h5>
                                    <p class="mb-0" style="font-size: 12px; color: #6c757d;">Rincian kapasitas rawat inap per Kabupaten/Kota</p>
                                </div>
                                <div class="input-group input-group-sm" style="width: 200px;">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                                    <input type="text" class="form-control border-start-0 bg-light" placeholder="Cari daerah...">
                                </div>
                            </div>
                            
                            <div class="table-responsive p-3">
                                <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th class="text-secondary fw-semibold py-2 rounded-start">KABUPATEN / KOTA</th>
                                            <th class="text-secondary fw-semibold py-2 text-center">TOTAL PUSKESMAS</th>
                                            <th class="text-secondary fw-semibold py-2 text-center">RAWAT INAP</th>
                                            <th class="text-secondary fw-semibold py-2 text-center">NON-RAWAT INAP</th>
                                            <th class="text-secondary fw-semibold py-2 text-end rounded-end">RASIO PENDUDUK</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Logika Forelse Laravel yang Benar -->
                                        @forelse (isset($data) ? $data : [] as $p)
                                            <tr>
                                                <td class="fw-bold text-dark py-3">{{ $p->kabupaten_kota ?? 'Nama Daerah' }}</td>
                                                <td class="text-center fw-bold text-tosca">{{ $p->total_puskesmas ?? 0 }} Unit</td>
                                                <td class="text-center">{{ $p->rawat_inap ?? 0 }} Unit</td>
                                                <td class="text-center">{{ $p->non_rawat_inap ?? 0 }} Unit</td>
                                                <td class="text-end text-secondary">{{ number_format($p->rasio ?? 0, 0, ',', '.') }} Jiwa</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                                    Data belum dikirim dari Controller atau Database kosong.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Grafik Komparasi -->
                    <div class="col-lg-4">
                        <div class="card-custom h-100 p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h5 class="mb-1" style="font-weight: 700; color: #1a1a2e; font-size: 16px;">Distribusi Tertinggi</h5>
                                    <p class="mb-0" style="font-size: 12px; color: #6c757d;">Top 5 Kabupaten/Kota</p>
                                </div>
                                <i class="bi bi-bar-chart-fill text-muted" style="font-size: 20px;"></i>
                            </div>

                            <!-- Progress Bars -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1" style="font-size: 13px;">
                                    <span class="fw-semibold text-dark">Aceh Utara</span>
                                    <span class="fw-bold text-tosca">32 Unit</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-tosca-light" style="width: 100%; background-color: #0d9488 !important;"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1" style="font-size: 13px;">
                                    <span class="fw-semibold text-dark">Aceh Besar</span>
                                    <span class="fw-bold text-tosca">29 Unit</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" style="width: 90%; background-color: #0d9488 !important;"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1" style="font-size: 13px;">
                                    <span class="fw-semibold text-dark">Aceh Timur</span>
                                    <span class="fw-bold text-tosca">27 Unit</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" style="width: 84%; background-color: #0d9488 !important;"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1" style="font-size: 13px;">
                                    <span class="fw-semibold text-dark">Aceh Selatan</span>
                                    <span class="fw-bold text-tosca">27 Unit</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" style="width: 84%; background-color: #0d9488 !important;"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1" style="font-size: 13px;">
                                    <span class="fw-semibold text-dark">Pidie</span>
                                    <span class="fw-bold text-tosca">26 Unit</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" style="width: 81%; background-color: #0d9488 !important;"></div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top" style="font-size: 12px; color: #6c757d;">
                                Rata-rata kepemilikan faskes primer di tingkat kabupaten adalah <strong class="text-dark">15.7 Unit</strong>.
                            </div>
                        </div>
                    </div>

                </div>

                <footer class="mt-4 pb-2 text-center" style="font-size: 13px; color: #8892a4;">
                    Portal Data Warehouse Provinsi Aceh &copy; 2024
                </footer>

            </div>
        </div>
    </div>

    <!-- Script Wajib Bootstrap dan landing.js milik tim -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>