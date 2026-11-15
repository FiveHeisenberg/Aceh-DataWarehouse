@extends('layouts.app')

@section('title', 'Indikator Pelayanan')

@section('content')
@php
    $tahunTerpilih = $tahunAktif;
    $daftarTahun   = $tahunList;

    $status = [
        'sembuh'    => ['label' => 'Sembuh',    'warna' => 'green'],
        'perawatan' => ['label' => 'Perawatan', 'warna' => 'blue'],
        'dirujuk'   => ['label' => 'Dirujuk',   'warna' => 'amber'],
        'meninggal' => ['label' => 'Meninggal', 'warna' => 'red'],
    ];

    $fmt = fn ($n) => number_format($n, 1, ',', '.');
    $pctDari = fn ($bagian, $total) => $total > 0 ? round(($bagian / $total) * 100, 1) : 0.0;
@endphp

<div class="ip-page">

    {{-- Breadcrumb + filter tahun --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item">Kesehatan</li>
                <li class="breadcrumb-item active" aria-current="page">Indikator Pelayanan</li>
            </ol>
        </nav>

        <form method="GET" action="{{ route('kesehatan.indikator-pelayanan') }}" class="m-0">
            <select name="tahun" class="form-select form-select-sm w-auto fw-semibold"
                    aria-label="Pilih tahun" onchange="this.form.submit()">
                @foreach ($daftarTahun as $th)
                    <option value="{{ $th }}" @selected($th === $tahunTerpilih)>{{ $th }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <h1 class="visually-hidden">Indikator Pelayanan Kesehatan</h1>

    {{-- KPI --}}
    <div class="row g-3 mb-4">
        @foreach ($kpi as $item)
            <div class="col-12 col-md-4">
                <div class="ip-card ip-kpi h-100">
                    <p class="ip-kpi-label">{{ $item['label'] }}</p>
                    <p class="ip-kpi-value">{{ $fmt($item['nilai']) }}%</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
         {{-- CARD KIRI — Metrik per Kab/Kota + Dropdown --}}
    <div class="col-12 col-lg-6">
        <div class="ip-card ip-panel">

            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-1">
                <div>
                    <h2 class="ip-panel-title" id="ip-metric-label">Tingkat Kesembuhan</h2>
                    <p class="ip-panel-sub" id="ip-metric-sub">Berdasarkan Kabupaten / Kota</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="dropdown">
                        <button class="ip-dropdown-btn dropdown-toggle"
                                type="button"
                                id="ip-metric-dropdown"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <span id="ip-dropdown-label">Tingkat Kesembuhan</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end ip-dropdown-menu"
                            aria-labelledby="ip-metric-dropdown">
                            <li>
                                <button class="dropdown-item ip-dropdown-item active"
                                        data-metric="kesembuhan"
                                        data-label="Tingkat Kesembuhan"
                                        data-sub="Berdasarkan Kabupaten / Kota">
                                    Tingkat Kesembuhan
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item ip-dropdown-item"
                                        data-metric="cfr"
                                        data-label="Case Fatality Rate (CFR)"
                                        data-sub="Proporsi kasus meninggal dari kasus selesai">
                                    Case Fatality Rate (CFR)
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item ip-dropdown-item"
                                        data-metric="rujukan"
                                        data-label="Tingkat Rujukan"
                                        data-sub="Proporsi kasus dirujuk dari kasus selesai">
                                    Tingkat Rujukan
                                </button>
                            </li>
                        </ul>
                    </div>

                    <button type="button" class="ip-sort-btn" id="ip-sort-btn"
                            aria-pressed="false"
                            aria-label="Urutkan tertinggi ke terendah"
                            title="Urutkan tertinggi ke terendah">
                        <i class="bi bi-arrow-down-up" id="ip-sort-icon" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="ip-bar-list mt-3" id="ip-bar-list">
                @forelse ($wilayah as $w)
                    <div class="ip-bar-row"
                         data-kesembuhan="{{ $w['kesembuhan'] }}"
                         data-level-kesembuhan="{{ $w['level_kesembuhan'] }}"
                         data-cfr="{{ $w['cfr'] }}"
                         data-level-cfr="{{ $w['level_cfr'] }}"
                         data-rujukan="{{ $w['rujukan_pct'] }}"
                         data-level-rujukan="{{ $w['level_rujukan'] }}">

                        <div class="d-flex justify-content-between align-items-baseline mb-1">
                            <span class="ip-name">{{ $w['nama'] }}</span>
                            <span class="ip-pct-label fw-semibold ip-text-{{ $w['level_kesembuhan'] }}"
                                  style="min-width: 3.5rem; text-align: right;">
                                {{ $fmt($w['kesembuhan']) }}%
                            </span>
                        </div>

                        <div class="ip-bar-track"
                             role="progressbar"
                             aria-label="Tingkat kesembuhan {{ $w['nama'] }}"
                             aria-valuenow="{{ $w['kesembuhan'] }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            <div class="ip-bar-fill ip-bg-{{ $w['level_kesembuhan'] }}"
                                 style="width: {{ $w['kesembuhan'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-4">Belum ada data pada tahun ini.</p>
                @endforelse
            </div>

            <div class="ip-legend mt-3" id="ip-legend-kesembuhan">
                <span><span class="ip-dot ip-bg-green"></span>&ge;80%</span>
                <span><span class="ip-dot ip-bg-amber"></span>60&ndash;79%</span>
                <span><span class="ip-dot ip-bg-red"></span>&lt;60%</span>
            </div>
            <div class="ip-legend mt-3 d-none" id="ip-legend-cfr">
                <span><span class="ip-dot ip-bg-green"></span>CFR &le;2%</span>
                <span><span class="ip-dot ip-bg-amber"></span>CFR 2&ndash;5%</span>
                <span><span class="ip-dot ip-bg-red"></span>CFR &gt;5%</span>
            </div>
            <div class="ip-legend mt-3 d-none" id="ip-legend-rujukan">
                <span><span class="ip-dot ip-bg-green"></span>Rujukan &le;10%</span>
                <span><span class="ip-dot ip-bg-amber"></span>Rujukan 10&ndash;20%</span>
                <span><span class="ip-dot ip-bg-red"></span>Rujukan &gt;20%</span>
            </div>
        </div>
    </div>

    {{-- CARD KANAN — Distribusi Status Akhir per Kab/Kota --}}
    <div class="col-12 col-lg-6">
        <div class="ip-card ip-panel">
            <h2 class="ip-panel-title">Status Akhir Kasus per Kab/Kota</h2>
            <p class="ip-panel-sub mb-3">
                Distribusi kasus: Sembuh, Dalam Perawatan, Dirujuk, dan Meninggal
            </p>

            @foreach ($wilayah as $w)
                @php
                    $dist    = collect($status)->mapWithKeys(fn ($s, $k) => [$k => $pctDari($w[$k], $w['total'])])->all();
                    $ringkas = collect($status)
                        ->map(fn ($s, $k) => $s['label'] . ' ' . $fmt($dist[$k]) . '%')
                        ->implode(', ');
                @endphp
                <div class="ip-row mb-3">
                    <div class="d-flex justify-content-between align-items-baseline mb-1">
                        <span class="ip-name">{{ $w['nama'] }}</span>
                        <span class="ip-total">Total: 100%</span>
                    </div>
                    <div class="ip-stack"
                         role="img"
                         aria-label="{{ $w['nama'] }}: {{ $ringkas }}">
                        @foreach ($status as $key => $s)
                            <div class="ip-seg ip-bg-{{ $s['warna'] }}"
                                 style="width: {{ $dist[$key] }}%"
                                 title="{{ $s['label'] }}: {{ $fmt($dist[$key]) }}%">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="ip-legend mt-2">
                @foreach ($status as $s)
                    <span><span class="ip-dot ip-bg-{{ $s['warna'] }}"></span>{{ $s['label'] }}</span>
                @endforeach
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
.ip-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 8px rgba(0,0,0,.04);
}

/* KPI dipertahankan */
.ip-kpi { padding: 1rem 1.25rem; }
.ip-kpi-label { font-size: 1.05rem; color: #1f2937; margin: 0; }
.ip-kpi-value { font-size: 2.5rem; font-weight: 700; line-height: 1.2; color: #111827; margin: 0; }

.ip-panel { height: 100%; }

.ip-panel-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1a1a2e;
    margin: 0 0 .15rem;
    line-height: 1.3;
}
.ip-panel-sub {
    font-size: .8rem;
    color: #6b7280;
    margin: 0;
}
.ip-name {
    font-size: .875rem;
    font-weight: 600;
    color: #374151;
}
.ip-total {
    font-size: .78rem;
    color: #9ca3af;
}
.ip-pct-label {
    font-size: .9rem;
}

.ip-dropdown-btn {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .35rem .75rem;
    font-size: .82rem;
    font-weight: 500;
    color: #374151;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    white-space: nowrap;
    cursor: pointer;
    transition: background .15s, border-color .15s;
}
.ip-dropdown-btn:hover,
.ip-dropdown-btn:focus {
    background: #f3f4f6;
    border-color: #d1d5db;
    outline: none;
}
.ip-dropdown-menu {
    min-width: 13rem;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(0,0,0,.10);
    padding: .35rem;
}
.ip-dropdown-item {
    border-radius: 6px;
    font-size: .84rem;
    padding: .45rem .75rem;
    color: #374151;
}
.ip-dropdown-item:hover,
.ip-dropdown-item:focus {
    background: #f3f4f6;
    color: #111827;
}
.ip-dropdown-item.active {
    background: #eff6ff;
    color: #1d4ed8;
    font-weight: 600;
}

.ip-sort-btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .35rem .65rem;
    font-size: .82rem;
    font-weight: 500;
    color: #374151;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    white-space: nowrap;
    cursor: pointer;
    transition: background .15s, border-color .15s, color .15s;
}
.ip-sort-btn:hover,
.ip-sort-btn:focus {
    background: #f3f4f6;
    border-color: #d1d5db;
    outline: none;
}
.ip-sort-btn[aria-pressed="true"] {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1d4ed8;
}

.ip-bar-list {
    display: flex;
    flex-direction: column;
    gap: .85rem;
}
.ip-bar-track {
    height: 10px;
    background: #f3f4f6;
    border-radius: 999px;
    overflow: hidden;
}
.ip-bar-fill {
    height: 100%;
    border-radius: 999px;
    transition: width .4s cubic-bezier(.4,0,.2,1), background-color .3s;
}

.ip-stack {
    display: flex;
    height: 12px;
    border-radius: 999px;
    overflow: hidden;
    gap: 1px;
}
.ip-seg {
    height: 100%;
    transition: width .3s;
}

.ip-bg-green  { background-color: #22c55e; }
.ip-bg-amber  { background-color: #f59e0b; }
.ip-bg-red    { background-color: #ef4444; }
.ip-bg-blue   { background-color: #3b82f6; }

.ip-text-green { color: #16a34a; }
.ip-text-amber { color: #d97706; }
.ip-text-red   { color: #dc2626; }

.ip-legend {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem 1rem;
    font-size: .78rem;
    color: #6b7280;
}
.ip-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: .3rem;
    vertical-align: middle;
}
</style>
@endpush

@push('scripts')
<script>
(function () {
    const colorMap = {
        green : { bar: '#22c55e', text: '#16a34a' },
        amber : { bar: '#f59e0b', text: '#d97706' },
        red   : { bar: '#ef4444', text: '#dc2626' },
    };

    const metricLabel = {
        kesembuhan: 'Tingkat kesembuhan',
        cfr: 'Case Fatality Rate',
        rujukan: 'Tingkat rujukan',
    };

    let activeMetric = 'kesembuhan';
    let sortDir = null;

    function fmt(v) {
        return parseFloat(v).toLocaleString('id-ID', {
            minimumFractionDigits: 1,
            maximumFractionDigits: 1
        });
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function renderMetric(metric) {
        const rows = document.querySelectorAll('#ip-bar-list .ip-bar-row');

        rows.forEach(row => {
            const val   = parseFloat(row.dataset[metric]);
            const level = row.dataset['level' + capitalize(metric)];
            const color = colorMap[level] ?? colorMap.red;

            const lbl = row.querySelector('.ip-pct-label');
            lbl.textContent = fmt(val) + '%';
            lbl.style.color = color.text;

            const fill = row.querySelector('.ip-bar-fill');
            fill.style.width = Math.min(val, 100) + '%';
            fill.style.backgroundColor = color.bar;

            const track = row.querySelector('.ip-bar-track');
            if (track) {
                const nama = row.querySelector('.ip-name').textContent;
                track.setAttribute('aria-valuenow', val);
                track.setAttribute('aria-label', metricLabel[metric] + ' ' + nama);
            }
        });

        ['kesembuhan', 'cfr', 'rujukan'].forEach(m => {
            const leg = document.getElementById('ip-legend-' + m);
            if (leg) leg.classList.toggle('d-none', m !== metric);
        });
    }

    function sortRows() {
        if (!sortDir) return;

        const list = document.getElementById('ip-bar-list');
        if (!list) return;

        const rows = Array.from(list.querySelectorAll('.ip-bar-row'));

        rows.sort((a, b) => {
            const va = parseFloat(a.dataset[activeMetric]) || 0;
            const vb = parseFloat(b.dataset[activeMetric]) || 0;
            return sortDir === 'desc' ? vb - va : va - vb;
        });

        rows.forEach(row => list.appendChild(row));
    }

    function updateSortButton() {
        const btn = document.getElementById('ip-sort-btn');
        const icon = document.getElementById('ip-sort-icon');
        if (!btn || !icon) return;

        if (sortDir === 'desc') {
            icon.className = 'bi bi-sort-down';
            btn.title = 'Urutkan terendah ke tertinggi';
            btn.setAttribute('aria-label', 'Urutkan terendah ke tertinggi');
            btn.setAttribute('aria-pressed', 'true');
        } else if (sortDir === 'asc') {
            icon.className = 'bi bi-sort-up';
            btn.title = 'Urutkan tertinggi ke terendah';
            btn.setAttribute('aria-label', 'Urutkan tertinggi ke terendah');
            btn.setAttribute('aria-pressed', 'true');
        } else {
            icon.className = 'bi bi-arrow-down-up';
            btn.title = 'Urutkan tertinggi ke terendah';
            btn.setAttribute('aria-label', 'Urutkan tertinggi ke terendah');
            btn.setAttribute('aria-pressed', 'false');
        }
    }

    document.querySelectorAll('.ip-dropdown-item').forEach(item => {
        item.addEventListener('click', function () {
            const metric = this.dataset.metric;
            const label  = this.dataset.label;
            const sub    = this.dataset.sub;

            document.querySelectorAll('.ip-dropdown-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');

            document.getElementById('ip-metric-label').textContent   = label;
            document.getElementById('ip-metric-sub').textContent     = sub;
            document.getElementById('ip-dropdown-label').textContent = label;

            activeMetric = metric;
            renderMetric(metric);
            sortRows();
        });
    });

    document.getElementById('ip-sort-btn')?.addEventListener('click', function () {
        sortDir = sortDir === 'desc' ? 'asc' : 'desc';
        sortRows();
        updateSortButton();
    });

    renderMetric('kesembuhan');
    updateSortButton();
})();
</script>
@endpush

@endsection