<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dispenda Provinsi Aceh</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

<div class="d-flex" style="min-height: 100vh;">

    <div class="d-flex flex-column" style="width: 260px; background-color: #ffffff; border-right: 1px solid #e0e0e0; position: fixed; top: 0; left: 0; bottom: 0; z-index: 1000;">

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

            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-people me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Penduduk</span>
                </a>
            </div>

            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-people me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Sosial</span>
                </a>
            </div>

            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-hospital me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Kesehatan</span>
                </a>
            </div>

            <div class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none p-2 rounded" style="color: #333;">
                    <i class="bi bi-mortarboard me-2" style="font-size: 18px; color: #555;"></i>
                    <span style="font-weight: 500; font-size: 14px;">Pendidikan</span>
                </a>
            </div>

            <div class="mb-1">
                <a href="{{ route('dispenda.index') }}" class="d-flex align-items-center text-decoration-none p-2 rounded" style="background-color: #e8f5f0; color: #0d9488; border-right: 3px solid #0d9488;">
                    <i class="bi bi-cash-coin me-2" style="font-size: 18px;"></i>
                    <span style="font-weight: 600; font-size: 14px;">Dispenda</span>
                </a>
            </div>

        </div>
    </div>

    <div class="flex-grow-1" style="margin-left: 260px;">

        <header class="px-4 py-3" style="background-color: #ffffff; border-bottom: 1px solid #e0e4f0;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="mb-1" style="font-weight: 800; color: #1a1a2e; font-size: 24px;">Data Dispenda Provinsi Aceh</h1>
                    <p class="mb-0" style="font-size: 13px; color: #8892a4;">Cakupan data: 2021–2023, 23 kabupaten/kota.</p>
                </div>
                <div>
                    <select id="filter-tahun" class="form-select" style="width: 150px; border: 1px solid #d0d8e0; border-radius: 6px; font-size: 14px;">
                        <option value="2024" selected>2024</option>
                        <option value="2023">2023</option>
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
                    </select>
                </div>
            </div>
        </header>

        <div class="p-4">

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); border-radius: 10px;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="mb-1" style="font-size: 13px; color: #8892a4; font-weight: 500;">Total Wajib Pajak</p>
                                    <h2 class="mb-0" style="font-weight: 800; color: #1a1a2e; font-size: 32px;">{{ number_format($data['total_wajib_pajak']) }}</h2>
                                </div>
                                <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: #e8f5f0;">
                                    <i class="bi bi-cash-coin" style="font-size: 28px; color: #0d9488;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); border-radius: 10px;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="mb-1" style="font-size: 13px; color: #8892a4; font-weight: 500;">Pertumbuhan</p>
                                    <h2 class="mb-0" style="font-weight: 800; color: #1a1a2e; font-size: 32px;">{{ number_format($data['pertumbuhan'], 2) }}%</h2>
                                </div>
                                <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px; background-color: #fef3c7;">
                                    <i class="bi bi-graph-up-arrow" style="font-size: 28px; color: #f59e0b;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); border-radius: 10px;">
                <div class="card-body p-4">
                    <h5 class="mb-3" style="font-weight: 700; color: #1a1a2e;">Data Dispenda</h5>
                    <p class="text-muted">Halaman ini sedang dalam pengembangan.</p>
                </div>
            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
