@extends('layouts.app')

@section('content')

<!-- Breadcrumb + Filter -->
<div class="d-flex align-items-center justify-content-between mb-2">
    <nav style="font-size: 15px; color: #5a6577;">
        <a href="/index" class="text-decoration-none" style="color: #5a6577;">Home</a>
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
                    </div>
                </div>
            </div>

            <!-- Card 2: KK Tertinggi -->
            <div class="col-md-6">
                <div class="card h-100" style="border: 1px solid #e0e4f0; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); background-color: #ffffff;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="text-uppercase" style="font-size: 12px; font-weight: 700; color: #5a6577; letter-spacing: 1px;">Jumlah KK Terbanyak</span>
                        </div>
                        <div class="mb-1">
                            <strong id="stat-kk-terbanyak-jumlah" style="font-size: 38px; font-weight: 800; color: #1a1a2e;">—</strong>
                        </div>
                        <div class="d-flex align-items-center justify-content-between pt-3" style="border-top: 1px solid #e8e8e8;">
                            <span id="stat-kk-terbanyak-nama" class="badge rounded-pill" style="background-color: #e8f5f0; color: #0d9488; font-size: 13px; font-weight: 700; padding: 6px 12px;">-</span>
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

                <div id="distribusi-chart-wrapper" class="d-none" style="max-height: 520px; overflow-y: auto; padding-right: 6px;">
                    <div id="distribusi-list"></div>
                </div>
                <div id="distribusi-kosong" class="text-center py-4 text-muted">Memuat data...</div>
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

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Custom JS Kartu Keluarga -->
<script src="{{ asset('js/Penduduk/kartu_keluarga.js') }}"></script>

@endsection