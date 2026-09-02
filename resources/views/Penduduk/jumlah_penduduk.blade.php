<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <select class="form-select" style="width: 150px; border: 1px solid #d0d8e0; border-radius: 6px; font-size: 14px;">
                        <option value="2023" selected>2023</option>
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
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
                                <strong style="font-size: 32px; font-weight: 800; color: #1a1a2e;">5.482.527</strong>
                            </div>
                            <small style="color: #8892a4; font-size: 13px;">dalam jiwa, tahun 2023</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                        <div class="card-body p-4">
                            <div class="text-uppercase mb-2" style="font-size: 12px; font-weight: 700; color: #5a6577; letter-spacing: 0.5px;">Laju Pertumbuhan</div>
                            <div class="d-flex align-items-baseline mb-1">
                                <strong style="font-size: 32px; font-weight: 800; color: #1a1a2e;">+1.36%</strong>
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
                        <div class="p-3" style="background-color: #ffffff; border-top: 1px solid #e0e4f0; border-radius: 0 0 12px 12px;">
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
                        </div>
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
                        <small style="color: #8892a4; font-size: 13px;">23 baris</small>
                    </div>
                    <div class="position-relative" style="width: 250px;">
                        <i class="bi bi-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #8892a4;"></i>
                        <input type="search" class="form-control ps-5" placeholder="Cari wilayah..." style="border-radius: 6px; border: 1px solid #d0d8e0; font-size: 14px;">
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
                            <tbody>
                                <tr>
                                    <td class="px-4 py-3">Kabupaten Aceh Utara</td>
                                    <td class="px-4 py-3 text-end">2023</td>
                                    <td class="px-4 py-3 text-end">624.899</td>
                                    <td class="px-4 py-3 text-end">jiwa</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">Kabupaten Bireuen</td>
                                    <td class="px-4 py-3 text-end">2023</td>
                                    <td class="px-4 py-3 text-end">453.242</td>
                                    <td class="px-4 py-3 text-end">jiwa</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">Kabupaten Pidie</td>
                                    <td class="px-4 py-3 text-end">2023</td>
                                    <td class="px-4 py-3 text-end">448.085</td>
                                    <td class="px-4 py-3 text-end">jiwa</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">Kabupaten Aceh Timur</td>
                                    <td class="px-4 py-3 text-end">2023</td>
                                    <td class="px-4 py-3 text-end">438.126</td>
                                    <td class="px-4 py-3 text-end">jiwa</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">Kabupaten Aceh Besar</td>
                                    <td class="px-4 py-3 text-end">2023</td>
                                    <td class="px-4 py-3 text-end">422.373</td>
                                    <td class="px-4 py-3 text-end">jiwa</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">Kabupaten Aceh Tamiang</td>
                                    <td class="px-4 py-3 text-end">2023</td>
                                    <td class="px-4 py-3 text-end">305.217</td>
                                    <td class="px-4 py-3 text-end">jiwa</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">Kota Banda Aceh</td>
                                    <td class="px-4 py-3 text-end">2023</td>
                                    <td class="px-4 py-3 text-end">261.969</td>
                                    <td class="px-4 py-3 text-end">jiwa</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">Kabupaten Aceh Selatan</td>
                                    <td class="px-4 py-3 text-end">2023</td>
                                    <td class="px-4 py-3 text-end">239.475</td>
                                    <td class="px-4 py-3 text-end">jiwa</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">Kabupaten Aceh Tenggara</td>
                                    <td class="px-4 py-3 text-end">2023</td>
                                    <td class="px-4 py-3 text-end">230.890</td>
                                    <td class="px-4 py-3 text-end">jiwa</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-3">Kabupaten Aceh Tengah</td>
                                    <td class="px-4 py-3 text-end">2023</td>
                                    <td class="px-4 py-3 text-end">223.833</td>
                                    <td class="px-4 py-3 text-end">jiwa</td>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ==================== LEAFLET MAP ====================
    const map = L.map('aceh-map').setView([4.6951, 96.7494], 8);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Dummy GeoJSON untuk wilayah Aceh (simplified)
    const acehRegions = [
        { name: 'Aceh Utara', coords: [5.2, 97.0], population: 624899 },
        { name: 'Bireuen', coords: [5.1, 96.5], population: 453242 },
        { name: 'Pidie', coords: [5.0, 96.0], population: 448085 },
        { name: 'Aceh Timur', coords: [4.8, 97.5], population: 438126 },
        { name: 'Aceh Besar', coords: [5.3, 95.5], population: 422373 },
        { name: 'Banda Aceh', coords: [5.55, 95.32], population: 261969 }
    ];

    // Tambahkan markers dengan warna berdasarkan populasi
    acehRegions.forEach(region => {
        const color = region.population > 500000 ? '#dc2626' : 
                      region.population > 400000 ? '#ea580c' : 
                      region.population > 300000 ? '#f59e0b' : '#10b981';
        
        L.circleMarker(region.coords, {
            radius: 8,
            fillColor: color,
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.8
        }).addTo(map).bindPopup(`<b>${region.name}</b><br>Jumlah: ${region.population.toLocaleString('id-ID')} jiwa`);
    });

    // ==================== CHART.JS TREND ====================
    const ctx = document.getElementById('trendChart').getContext('2d');
    const trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['2021', '2022', '2023'],
            datasets: [{
                label: 'Jumlah Penduduk',
                data: [5335000, 5410000, 5482527],
                borderColor: '#0d9488',
                backgroundColor: 'rgba(13, 148, 136, 0.1)',
                borderWidth: 3,
                pointBackgroundColor: '#0d9488',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1a1a2e',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Jumlah: ' + context.parsed.y.toLocaleString('id-ID') + ' jiwa';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 5300000,
                    max: 5500000,
                    ticks: {
                        callback: function(value) {
                            return (value / 1000000).toFixed(2) + 'M';
                        },
                        font: {
                            size: 11
                        },
                        color: '#8892a4'
                    },
                    grid: {
                        color: '#e0e4f0',
                        drawBorder: false
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12
                        },
                        color: '#5a6577'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

});
</script>

</body>
</html>