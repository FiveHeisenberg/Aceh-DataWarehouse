@php
    $rp = fn ($v) => 'Rp ' . number_format((float) ($v ?? 0), 0, ',', '.');
    $strip = fn (?string $n) => preg_replace('/^(Kabupaten |Kota )\s*/i', '', (string) $n);
    $status = $filters['status'];
    $tahun = $filters['tahun'];

    // @json() meledak di koma, jadi config modal harus lewat variabel.
    $detailConfig = [
        'urlTemplate' => route('dispenda.tagihan.detail', ['id' => '__ID__']),
        'sections' => [
            [
                'title' => 'Informasi Tagihan',
                'status' => ['key' => 'status_tagihan', 'ok' => 'Lunas', 'bad' => 'Belum Lunas'],
                'fields' => [
                    ['label' => 'ID Tagihan', 'key' => 'id_tagihan', 'type' => 'mono'],
                    ['label' => 'Tahun Pajak', 'key' => 'tahun_pajak'],
                    ['label' => 'Nominal Tagihan', 'key' => 'nominal_tagihan', 'type' => 'money'],
                    ['label' => 'Jatuh Tempo', 'key' => 'tanggal_jatuh_tempo', 'type' => 'date'],
                ],
            ],
            [
                'title' => 'Wajib Pajak',
                'fields' => [
                    ['label' => 'NIK', 'key' => 'nik_wp', 'type' => 'mono'],
                    ['label' => 'NPWPD', 'key' => 'npwpd', 'type' => 'mono'],
                    ['label' => 'Nama Lengkap', 'key' => 'nama_wp', 'span' => true],
                    ['label' => 'Alamat', 'key' => 'alamat', 'span' => true, 'type' => 'plain'],
                    ['label' => 'Kabupaten/Kota', 'key' => 'nama_kabupaten_kota', 'type' => 'strip'],
                ],
            ],
            [
                'title' => 'Objek Pajak',
                'showIfAny' => ['nama_pajak', 'rincian_objek'],
                'fields' => [
                    ['label' => 'Jenis Pajak', 'key' => 'nama_pajak', 'showIf' => true],
                    ['label' => 'Tarif', 'key' => 'tarif_persentase', 'type' => 'percent', 'showIf' => true],
                    ['label' => 'Nomor Identitas Aset', 'key' => 'nomor_identitas_aset', 'type' => 'mono', 'showIf' => true],
                    ['label' => 'Nilai Aset', 'key' => 'nilai_aset', 'type' => 'money', 'showIf' => true],
                    ['label' => 'Rincian Objek', 'key' => 'rincian_objek', 'span' => true, 'type' => 'plain', 'showIf' => true],
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
        .badge-lunas { background-color: #dcfce7; color: #15803d; }
        .badge-belum { background-color: #fee2e2; color: #b91c1c; }
        .modal-field-label { font-size: .75rem; color: #6b7280; margin-bottom: .125rem; }
        .modal-field-value { font-size: .875rem; font-weight: 500; color: #111827; margin-bottom: 0; word-break: break-word; }
    </style>
@endpush

@section('content')
        <div class="d-flex flex-column gap-4 mx-auto" style="max-width: 1400px;">

            <div>
                <p class="mb-2" style="font-size: .875rem; color: #6b7280;"><span class="fw-medium" style="color: #0d9488;">Pendapatan Daerah</span> / Data Tagihan</p>
                <h1 class="h3 fw-bold mb-0" style="color: #111827;">Data Tagihan Pajak</h1>
                <p class="mb-0 mt-1" style="font-size: .875rem; color: #6b7280;">Kelola dan pantau tagihan pajak daerah</p>
            </div>

            {{-- STATISTIC CARDS --}}
            <div class="row g-3">
                <div class="col-12 col-lg-4">
                    <div class="panel-card d-flex align-items-center gap-3 p-3 h-100">
                        <div class="icon-badge bg-body-secondary" style="color: #6b7280;"><i class="bi bi-file-text"></i></div>
                        <div class="min-w-0">
                            <p class="stat-label mb-0">Total Seluruh Tagihan</p>
                            <p class="stat-value mt-1 mb-0">{{ number_format((int) ($statistics['totalTagihan'] ?? 0), 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="panel-card d-flex align-items-center gap-3 p-3 h-100">
                        <div class="icon-badge" style="background-color: #dcfce7; color: #15803d;"><i class="bi bi-check-circle"></i></div>
                        <div class="min-w-0">
                            <p class="stat-label mb-0">Total Nominal Lunas</p>
                            <p class="stat-value mt-1 mb-0 text-truncate">{{ $rp($statistics['totalLunas'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="panel-card d-flex align-items-center gap-3 p-3 h-100">
                        <div class="icon-badge" style="background-color: #fee2e2; color: #b91c1c;"><i class="bi bi-exclamation-circle"></i></div>
                        <div class="min-w-0">
                            <p class="stat-label mb-0">Total Nominal Belum Lunas</p>
                            <p class="stat-value mt-1 mb-0 text-truncate">{{ $rp($statistics['totalBelumLunas'] ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FILTER BAR --}}
            <form method="GET" action="{{ route('dispenda.tagihan') }}" class="panel-card d-flex flex-wrap align-items-center gap-2 p-3" id="filter-tagihan">
                <div class="position-relative flex-grow-1" style="min-width: 250px;">
                    <label for="filter-search" class="visually-hidden">Cari tagihan</label>
                    <i class="bi bi-search position-absolute" style="left: .75rem; top: 50%; transform: translateY(-50%); color: #6b7280;"></i>
                    <input type="search" name="search" id="filter-search" class="form-control form-control-sm ps-5" style="padding-left: 2.25rem;" placeholder="Cari ID Tagihan atau Nama..." value="{{ $filters['search'] }}" data-debounce>
                </div>

                <span class="fw-medium" style="font-size: .875rem; color: #111827;">Filter:</span>

                <label for="filter-status" class="visually-hidden">Status</label>
                <select name="status" id="filter-status" class="form-select form-select-sm" style="width: auto;" data-autosubmit>
                    @foreach (['Semua', 'Lunas', 'Belum Lunas'] as $opt)
                        <option value="{{ $opt }}" @selected($status === $opt)>{{ $opt }}</option>
                    @endforeach
                </select>

                <label for="filter-thn" class="visually-hidden">Tahun</label>
                <select name="tahun" id="filter-thn" class="form-select form-select-sm" style="width: auto;" data-autosubmit>
                    @foreach ($availableYears as $y)
                        <option value="{{ $y }}" @selected((int) $y === (int) $tahun)>{{ $y }}</option>
                    @endforeach
                </select>

                <label for="filter-kab" class="visually-hidden">Kabupaten/Kota</label>
                <select name="kabupaten" id="filter-kab" class="form-select form-select-sm" style="width: auto;" data-autosubmit>
                    <option value="Semua" @selected($filters['kabupaten'] === 'Semua')>Semua</option>
                    @foreach ($wilayahOptions as $w)
                        <option value="{{ $w['id'] }}" @selected((string) $filters['kabupaten'] === (string) $w['id'])>{{ $strip($w['name']) }}</option>
                    @endforeach
                </select>

                <label for="filter-kategori" class="visually-hidden">Kategori pajak</label>
                <select name="kategori_pajak" id="filter-kategori" class="form-select form-select-sm" style="width: auto;" data-autosubmit>
                    <option value="Semua" @selected($filters['kategori_pajak'] === 'Semua')>Semua</option>
                    @foreach ($kategoriPajakOptions as $k)
                        <option value="{{ $k['id'] }}" @selected((string) $filters['kategori_pajak'] === (string) $k['id'])>{{ $k['name'] }}</option>
                    @endforeach
                </select>

                <button type="submit" formaction="{{ route('dispenda.tagihan.export') }}" class="btn btn-sm text-white" style="background-color: #0d9488; border-color: #0d9488;">
                    <i class="bi bi-download me-1"></i>Export Data
                </button>
            </form>

            {{-- TABEL --}}
            <div class="panel-card overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-lite">
                            <tr>
                                <th scope="col" class="ps-3">ID Tagihan</th>
                                <th scope="col">Wajib Pajak</th>
                                <th scope="col">Daerah</th>
                                <th scope="col" class="text-end">Nominal</th>
                                <th scope="col">Jatuh Tempo</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tagihan as $row)
                                <tr>
                                    <td class="ps-3 font-monospace fw-medium" style="font-size: .875rem; color: #111827;">{{ $row->id_tagihan }}</td>
                                    <td style="font-size: .875rem; color: #111827;">{{ $row->nama_wp }}</td>
                                    <td style="font-size: .875rem; color: #111827;">{{ $strip($row->nama_kabupaten_kota) }}</td>
                                    <td class="text-end fw-medium" style="font-size: .875rem; color: #111827;">{{ $rp($row->nominal_tagihan) }}</td>
                                    <td style="font-size: .875rem; color: #111827;">{{ $row->tanggal_jatuh_tempo ? \Illuminate\Support\Carbon::parse($row->tanggal_jatuh_tempo)->translatedFormat('j M Y') : '-' }}</td>
                                    <td>
                                        @if ($row->status_tagihan === 'Lunas')
                                            <span class="badge rounded-pill badge-lunas">{{ $row->status_tagihan }}</span>
                                        @else
                                            <span class="badge rounded-pill badge-belum">{{ $row->status_tagihan }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-detail="{{ $row->id_tagihan }}"
                                            data-modal-title="Detail Tagihan Pajak">
                                            <i class="bi bi-eye me-1"></i>Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4" style="font-size: .875rem; color: #6b7280;">Tidak ada data tagihan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($tagihan->total() > 0)
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 border-top px-3 py-2">
                        <p class="mb-0" style="font-size: .875rem; color: #6b7280;">
                            Menampilkan {{ $tagihan->firstItem() }} - {{ $tagihan->lastItem() }} dari {{ number_format($tagihan->total(), 0, ',', '.') }} data
                        </p>
                        <div>{{ $tagihan->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
                    </div>
                @endif
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
<script type="application/json" id="detail-config">@json($detailConfig)</script>

<script src="{{ asset('js/Dispenda/filters.js') }}"></script>
<script src="{{ asset('js/Dispenda/detail-modal.js') }}"></script>
@endpush
