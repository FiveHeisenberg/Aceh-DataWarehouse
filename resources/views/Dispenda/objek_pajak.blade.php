@php
    $rp = fn ($v) => 'Rp ' . number_format((float) ($v ?? 0), 0, ',', '.');
    $strip = fn (?string $n) => preg_replace('/^(Kabupaten |Kota )\s*/i', '', (string) $n);
    $kategori = $filters['kategori'];
    $kabupaten = $filters['kabupaten'];

    // dim_objek_pajak hanya punya satu tahun load, jadi chart tren sering cuma 1 batang.
    // 1 titik = bukan tren. Tampilkan empty state, jangan render chart yang menipu.
    $trend = $chartData['trendPendaftaran'];
    $trendPoints = $trend->count();
    $komposisi = $chartData['komposisiPotensi'];

    // @json() meledak di koma, jadi config modal harus lewat variabel.
    $detailConfig = [
        'urlTemplate' => route('dispenda.objek-pajak.detail', ['id' => '__ID__']),
        'sections' => [
            [
                'title' => 'Informasi Objek',
                'statusText' => 'Aktif',
                'fields' => [
                    ['label' => 'ID Objek', 'key' => 'id_objek', 'type' => 'mono'],
                    ['label' => 'Jenis Pajak', 'key' => 'nama_pajak'],
                    ['label' => 'Nomor Identitas Aset', 'key' => 'nomor_identitas_aset', 'type' => 'mono', 'showIf' => true],
                    ['label' => 'Tarif Pajak', 'key' => 'tarif_persentase', 'type' => 'percent', 'showIf' => true],
                    ['label' => 'Nilai Aset / Potensi Pajak', 'key' => 'nilai_aset', 'type' => 'money', 'span' => true],
                    ['label' => 'Rincian Objek', 'key' => 'rincian_objek', 'span' => true, 'type' => 'plain', 'showIf' => true],
                ],
            ],
            [
                'title' => 'Wajib Pajak (Pemilik)',
                'fields' => [
                    ['label' => 'NIK', 'key' => 'nik_wp', 'type' => 'mono'],
                    ['label' => 'NPWPD', 'key' => 'npwpd', 'type' => 'mono'],
                    ['label' => 'Nama Lengkap', 'key' => 'pemilik', 'span' => true],
                    ['label' => 'Alamat', 'key' => 'alamat', 'span' => true, 'type' => 'plain'],
                    ['label' => 'Kabupaten/Kota', 'key' => 'nama_kabupaten_kota', 'type' => 'strip'],
                ],
            ],
        ],
    ];
@endphp
@extends('layouts.app')

@push('styles')
    <style>
        .panel-card {
            background: #fff; border: 1px solid #e5e7eb; border-radius: .5rem;
            box-shadow: 0 1px 2px rgba(16, 24, 40, .05);
        }
        .stat-label { font-size: .75rem; font-weight: 500; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: #111827; }
        .icon-badge { width: 44px; height: 44px; border-radius: 999px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.25rem; }
        .thead-lite { background-color: #f9fafb; border-bottom: 1px solid #e5e7eb; }
        .thead-lite th { font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; }
        table tbody tr:hover { background-color: #f9fafb; }
        .badge-aktif { background-color: #dcfce7; color: #15803d; }
        .chart-empty { height: 250px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .25rem; padding: 0 1.5rem; text-align: center; }
        .modal-field-label { font-size: .75rem; color: #6b7280; margin-bottom: .125rem; }
        .modal-field-value { font-size: .875rem; font-weight: 500; color: #111827; margin-bottom: 0; word-break: break-word; }
    </style>
@endpush

@section('content')
        <div class="d-flex flex-column gap-4 mx-auto" style="max-width: 1400px;">

            <div>
                <p class="mb-2" style="font-size: .875rem; color: #6b7280;"><span class="fw-medium" style="color: #0d9488;">Pendapatan Daerah</span> / Objek Pajak</p>
                <h1 class="h3 fw-bold mb-0" style="color: #111827;">Master Data Objek Pajak</h1>
                <p class="mb-0 mt-1" style="font-size: .875rem; color: #6b7280;">Kelola dan pantau aset objek pajak daerah</p>
            </div>

            {{-- STATISTIC CARDS --}}
            <div class="row g-3">
                <div class="col-12 col-lg-4">
                    <div class="panel-card d-flex align-items-center gap-3 p-3 h-100">
                        <div class="icon-badge" style="background-color: #dbeafe; color: #1d4ed8;"><i class="bi bi-building"></i></div>
                        <div class="min-w-0">
                            <p class="stat-label mb-0">Total Objek Pajak</p>
                            <p class="stat-value mt-1 mb-0">{{ number_format((int) ($statistics['totalObjek'] ?? 0), 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="panel-card d-flex align-items-center gap-3 p-3 h-100">
                        <div class="icon-badge" style="background-color: #dcfce7; color: #15803d;"><i class="bi bi-currency-dollar"></i></div>
                        <div class="min-w-0">
                            <p class="stat-label mb-0">Total Potensi Pajak</p>
                            <p class="stat-value mt-1 mb-0 text-truncate">{{ $rp($statistics['totalPotensi'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="panel-card d-flex flex-column gap-3 p-3 h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-badge" style="background-color: #ede9fe; color: #6d28d9;"><i class="bi bi-graph-up-arrow"></i></div>
                            <div class="min-w-0">
                                <p class="stat-label mb-0">Pertumbuhan Objek</p>
                                <p class="stat-value mt-1 mb-0">{{ ($statistics['pertumbuhan'] ?? 0) > 0 ? '+' : '' }}{{ number_format((float) ($statistics['pertumbuhan'] ?? 0), 1, ',', '.') }}%</p>
                            </div>
                        </div>
                        @if (($statistics['objekTahunIni'] ?? null) !== null)
                            <p class="mb-0" style="font-size: .75rem; color: #6b7280;">{{ number_format((int) $statistics['objekTahunIni'], 0, ',', '.') }} objek masuk pada {{ $statistics['tahunAcuan'] }} (tahun berjalan)</p>
                        @else
                            <p class="mb-0" style="font-size: .75rem; color: #6b7280;">Pertumbuhan dibanding tahun sebelumnya</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CHARTS --}}
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="panel-card p-3 h-100">
                        <h3 class="mb-1" style="font-size: 1rem; font-weight: 600; color: #111827;">Tren Pendaftaran Objek</h3>
                        <p class="mb-3" style="font-size: .75rem; color: #6b7280;">Jumlah objek per tahun load data</p>
                        @if ($trendPoints === 0)
                            <div class="chart-empty">
                                <p class="mb-0 fw-medium" style="font-size: .875rem; color: #111827;">Tidak ada objek pajak yang sesuai filter</p>
                                <p class="mb-0" style="font-size: .75rem; color: #6b7280;">Longgarkan filter di Action Bar untuk melihat grafik.</p>
                            </div>
                        @elseif ($trendPoints < 2)
                            <div class="chart-empty">
                                <p class="mb-0 fw-medium" style="font-size: .875rem; color: #111827;">Data historis belum tersedia</p>
                                <p class="mb-0" style="font-size: .75rem; color: #6b7280;">
                                    Seluruh objek ({{ number_format((int) ($statistics['totalObjek'] ?? 0), 0, ',', '.') }} data) tercatat
                                    pada tahun {{ $trend->first()['tahun'] ?? 'Ã¢â‚¬â€' }} saja, sehingga tren tahunan belum dapat ditampilkan.
                                </p>
                            </div>
                        @else
                            <div style="height: 250px;"><canvas id="chart-tren-pendaftaran"></canvas></div>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="panel-card p-3 h-100">
                        <h3 class="mb-1" style="font-size: 1rem; font-weight: 600; color: #111827;">Komposisi Potensi per Kategori</h3>
                        <p class="mb-3" style="font-size: .75rem; color: #6b7280;">Distribusi nilai aset objek pajak</p>
                        @if ($komposisi->count() === 0)
                            <div class="chart-empty">
                                <p class="mb-0 fw-medium" style="font-size: .875rem; color: #111827;">Tidak ada objek pajak yang sesuai filter</p>
                                <p class="mb-0" style="font-size: .75rem; color: #6b7280;">Longgarkan filter di Action Bar untuk melihat grafik.</p>
                            </div>
                        @else
                            <div style="height: 250px;"><canvas id="chart-komposisi"></canvas></div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- FILTER BAR --}}
            <form method="GET" action="{{ route('dispenda.objek-pajak') }}" class="panel-card d-flex flex-wrap align-items-center gap-2 p-3" id="filter-objek">
                <div class="position-relative flex-grow-1" style="min-width: 250px;">
                    <label for="filter-search" class="visually-hidden">Cari objek pajak</label>
                    <i class="bi bi-search position-absolute" style="left: .75rem; top: 50%; transform: translateY(-50%); color: #6b7280;"></i>
                    <input type="search" name="search" id="filter-search" class="form-control form-control-sm" style="padding-left: 2.25rem;" placeholder="Cari ID Objek, Pemilik, atau Nomor Aset..." value="{{ $filters['search'] }}" data-debounce>
                </div>

                <span class="fw-medium" style="font-size: .875rem; color: #111827;">Filter:</span>

                <label for="filter-kategori" class="visually-hidden">Kategori pajak</label>
                <select name="kategori" id="filter-kategori" class="form-select form-select-sm" style="width: auto;" data-autosubmit>
                    <option value="Semua" @selected($kategori === 'Semua')>Semua</option>
                    @foreach ($kategoriOptions as $k)
                        <option value="{{ $k['id'] }}" @selected((string) $kategori === (string) $k['id'])>{{ $k['name'] }}</option>
                    @endforeach
                </select>

                <label for="filter-kab" class="visually-hidden">Kabupaten/Kota</label>
                <select name="kabupaten" id="filter-kab" class="form-select form-select-sm" style="width: auto;" data-autosubmit>
                    <option value="Semua" @selected($kabupaten === 'Semua')>Semua</option>
                    @foreach ($wilayahOptions as $w)
                        <option value="{{ $w['id'] }}" @selected((string) $kabupaten === (string) $w['id'])>{{ $strip($w['name']) }}</option>
                    @endforeach
                </select>

                {{-- dim_objek_pajak tidak punya kolom status, jadi filter ini tidak mengubah hasil --}}
                <label for="filter-status" class="visually-hidden">Status</label>
                <select name="status" id="filter-status" class="form-select form-select-sm" style="width: auto;" disabled title="dim_objek_pajak tidak memiliki kolom status">
                    <option value="Semua" @selected(($filters['status'] ?? 'Semua') === 'Semua')>Semua</option>
                    <option value="Aktif" @selected(($filters['status'] ?? '') === 'Aktif')>Aktif</option>
                    <option value="Non-Aktif" @selected(($filters['status'] ?? '') === 'Non-Aktif')>Non-Aktif</option>
                </select>

                <button type="submit" formaction="{{ route('dispenda.objek-pajak.export') }}" class="btn btn-sm text-white" style="background-color: #0d9488; border-color: #0d9488;">
                    <i class="bi bi-download me-1"></i>Export Data
                </button>
            </form>

            {{-- TABEL --}}
            <div class="panel-card overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-lite">
                            <tr>
                                <th scope="col" class="ps-3">ID Objek</th>
                                <th scope="col">Jenis Pajak</th>
                                <th scope="col">Wajib Pajak (Pemilik)</th>
                                <th scope="col">Lokasi/Alamat</th>
                                <th scope="col" class="text-end">Potensi</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($objekPajak as $row)
                                <tr>
                                    <td class="ps-3 font-monospace fw-medium" style="font-size: .875rem; color: #111827;">{{ $row->id_objek }}</td>
                                    <td style="font-size: .875rem; color: #111827;">{{ $row->nama_pajak }}</td>
                                    <td style="font-size: .875rem; color: #111827;">{{ $row->pemilik }}</td>
                                    <td style="font-size: .875rem; color: #111827; max-width: 240px;" title="{{ $row->alamat ?? '' }}"><div class="text-truncate">{{ $row->alamat ?? '-' }}</div></td>
                                    <td class="text-end fw-medium" style="font-size: .875rem; color: #111827;">{{ $rp($row->nilai_aset) }}</td>
                                    <td><span class="badge rounded-pill badge-aktif">Aktif</span></td>
                                    <td class="text-center pe-3">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-detail="{{ $row->id_objek }}"
                                            data-modal-title="Detail Objek Pajak">
                                            <i class="bi bi-eye me-1"></i>Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4" style="font-size: .875rem; color: #6b7280;">Tidak ada data objek pajak</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($objekPajak->total() > 0)
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 border-top px-3 py-2">
                        <p class="mb-0" style="font-size: .875rem; color: #6b7280;">
                            Menampilkan {{ $objekPajak->firstItem() }} - {{ $objekPajak->lastItem() }} dari {{ number_format($objekPajak->total(), 0, ',', '.') }} data
                        </p>
                        <div>{{ $objekPajak->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
                    </div>
                @endif
            </div>

            </div>
        </div>
        
        {{-- ==================== MODAL DETAIL ==================== --}}
        <div class="modal fade" id="modal-detail" tabindex="-1" aria-hidden="true" aria-labelledby="modal-detail-title">
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header position-sticky top-0" style="z-index: 10;">
                        <h2 class="modal-title h5 mb-0" id="modal-detail-title" style="color: #111827;">Detail</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body" id="modal-detail-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
<script type="application/json" id="data-trend-pendaftaran">@json($trend)</script>
<script type="application/json" id="data-komposisi">@json($komposisi)</script>
<script type="application/json" id="detail-config">@json($detailConfig)</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/Dispenda/filters.js') }}"></script>
<script src="{{ asset('js/Dispenda/detail-modal.js') }}"></script>
<script src="{{ asset('js/Dispenda/objek-pajak.js') }}"></script>
@endpush
