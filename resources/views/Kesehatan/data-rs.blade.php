@extends('layouts.app')

@section('content')
@php
    // ====== DATA STATIS ======
    $rs = [
        'nama'       => 'RS Zainal Abidin',
        'alamat'     => 'jln. H, Abdul Khadir',
        'telepon'    => '085163576166',
        'jenis'      => 'Rumah Sakit Umum',
        'kelas'      => 'A',
        'kepemilikan'=> 'Pemerintah Provinsi',
        'foto'       => null, // isi URL gambar jika sudah ada, contoh: asset('img/rs-zainal-abidin.jpg')
    ];

    // Tab Layanan
    $layanan = [
        'Pelayanan KIA/KB',
        'Infeksi Paru',
        'Pelayanan Gawat Darurat Umum 24 jam & 7 hari seminggu',
        'Penyakit dalam',
        'Kesehatan anak',
        'Bedah',
        'Obstetri dan ginekologi',
        'Anestesi',
        'Radiologi',
        'Patologi Klinik',
        'Patologi Anatomi',
        'Rehabilitasi Medik',
        'Kesehatan Jiwa',
        'Mata',
        'Telinga Hidung Tenggorokan',
        'Kulit dan Kelamin',
        'Gigi dan Mulut',
        'Saraf',
        'Jantung dan Pembuluh Darah',
        'Gizi Klinik',
        'Farmasi',
        'Laboratorium',
        'Bank Darah',
        'Hemodialisa',
        'Rawat Inap',
        'Rawat Jalan',
        'Intensive Care Unit (ICU)',
        'Ambulans',
    ];

    // Tab Fasilitas: [nama, jumlah]
    $fasilitas = [
        ['Tempat Tidur Rawat Inap', 320],
        ['Tempat Tidur ICU', 20],
        ['Ruang Operasi', 8],
        ['Ruang IGD', 1],
        ['Ruang Isolasi', 12],
        ['Ruang Bersalin', 4],
        ['Ruang Hemodialisa', 1],
        ['Laboratorium', 1],
        ['Ruang Radiologi', 2],
        ['Apotek', 1],
        ['Ambulans', 6],
        ['Ruang Rehabilitasi Medik', 1],
    ];

    // Tab Tenaga Medis: [jenis, jumlah]
    $tenagaMedis = [
        ['Dokter Spesialis', 64],
        ['Dokter Umum', 28],
        ['Dokter Gigi', 6],
        ['Perawat', 310],
        ['Bidan', 42],
        ['Apoteker', 14],
        ['Analis Kesehatan', 22],
        ['Radiografer', 10],
        ['Tenaga Gizi', 12],
        ['Terapis', 8],
        ['Tenaga Kesehatan Lainnya', 30],
    ];

    // Susun menjadi struktur yang dipakai JS
    $tabs = [
        'fasilitas' => [
            'kolom' => ['Fasilitas', 'Jumlah'],
            'baris' => array_map(fn($r) => [$r[0], $r[1]], $fasilitas),
        ],
        'layanan' => [
            'kolom' => ['Pelayanan'],
            'baris' => array_map(fn($r) => [$r], $layanan),
        ],
        'tenaga' => [
            'kolom' => ['Jenis Tenaga Medis', 'Jumlah'],
            'baris' => array_map(fn($r) => [$r[0], $r[1]], $tenagaMedis),
        ],
    ];
@endphp

<style>
    .rs-box { border: 2px solid #111827; border-radius: 1.5rem; padding: 1.5rem; background: #fff; }
    .rs-photo { width: 100%; aspect-ratio: 1 / 0.88; border: 2px solid #111827; border-radius: 1.5rem; overflow: hidden; background: #fff; }
    .rs-photo img { width: 100%; height: 100%; object-fit: cover; }
    .rs-info dt { font-weight: 700; font-size: .95rem; }
    .rs-info dd { margin-bottom: 1.1rem; }
    .btn-tab { border: 2px solid #111827; border-radius: 1rem; font-weight: 700; padding: .55rem 1rem; background: #fff; color: #111827; min-width: 130px; }
    .btn-tab:hover { background: #f3f4f6; }
    .btn-tab.active { background: #bdbdbd; color: #111827; }
    .tbl-rs thead th { background: #e7efff; font-size: .85rem; cursor: pointer; user-select: none; white-space: nowrap; }
    .tbl-rs thead th .sort { color: #9ca3af; font-size: .7rem; margin-left: .35rem; }
    .tbl-rs tbody td { font-size: .85rem; padding-top: .8rem; padding-bottom: .8rem; }
    .tbl-rs tbody tr:nth-child(even) { background: #fafbff; }
    .tbl-rs td.no { text-align: right; width: 90px; }
</style>

<div class="container-fluid py-3">
    <h3 class="fw-bold mb-3">Profile Rumah Sakit</h3>

    <div class="row g-4">
        {{-- Kartu profil --}}
        <div class="col-lg-4">
            <div class="rs-box h-100">
                <div class="rs-photo mb-3">
                    @if ($rs['foto'])
                        <img src="{{ $rs['foto'] }}" alt="{{ $rs['nama'] }}">
                    @endif
                </div>

                <h4 class="fw-bold text-center mb-4">{{ $rs['nama'] }}</h4>

                <dl class="rs-info mb-0">
                    <dt>Alamat:</dt>
                    <dd>{{ $rs['alamat'] }}</dd>

                    <dt>Telepon:</dt>
                    <dd>{{ $rs['telepon'] }}</dd>

                    <dt>Jenis Rumah Sakit:</dt>
                    <dd>{{ $rs['jenis'] }}</dd>

                    <dt>Kelas Rumah Sakit:</dt>
                    <dd>{{ $rs['kelas'] }}</dd>

                    <dt>Kepemilikan:</dt>
                    <dd class="mb-0">{{ $rs['kepemilikan'] }}</dd>
                </dl>
            </div>
        </div>

        {{-- Tab + tabel --}}
        <div class="col-lg-8">
            <div class="rs-box h-100">
                <div class="d-flex flex-wrap gap-3 mb-4" id="tabButtons">
                    <button type="button" class="btn-tab" data-tab="fasilitas">Fasilitas</button>
                    <button type="button" class="btn-tab active" data-tab="layanan">Layanan</button>
                    <button type="button" class="btn-tab" data-tab="tenaga">Tenaga Medis</button>
                </div>

                <div class="table-responsive border">
                    <table class="table tbl-rs mb-0">
                        <thead><tr id="theadRow"></tr></thead>
                        <tbody id="tbodyRows"></tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
                    <div class="small" id="infoEntries"></div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = @json($tabs);
        const PER_PAGE = 10;

        let tabAktif = 'layanan';
        const state = {};
        Object.keys(tabs).forEach(k => state[k] = { page: 1, sortCol: 0, sortDir: 'asc' }); // sortCol 0 = No.

        const thead = document.getElementById('theadRow');
        const tbody = document.getElementById('tbodyRows');
        const info  = document.getElementById('infoEntries');
        const pag   = document.getElementById('pagination');

        const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        const fmt = v => typeof v === 'number' ? v.toLocaleString('id-ID') : esc(v);

        function render() {
            const tab = tabs[tabAktif];
            const st  = state[tabAktif];

            // data + nomor asli
            let rows = tab.baris.map((r, i) => ({ no: i + 1, cells: r }));

            // sorting: kolom 0 = No., kolom 1.. = isi kolom
            rows.sort((a, b) => {
                let va, vb;
                if (st.sortCol === 0) { va = a.no; vb = b.no; }
                else { va = a.cells[st.sortCol - 1]; vb = b.cells[st.sortCol - 1]; }
                let cmp = (typeof va === 'number' && typeof vb === 'number')
                    ? va - vb
                    : String(va).localeCompare(String(vb), 'id');
                return st.sortDir === 'asc' ? cmp : -cmp;
            });

            const total = rows.length;
            const pages = Math.max(1, Math.ceil(total / PER_PAGE));
            if (st.page > pages) st.page = pages;
            const start = (st.page - 1) * PER_PAGE;
            const slice = rows.slice(start, start + PER_PAGE);

            // header
            const heads = ['No.', ...tab.kolom];
            thead.innerHTML = heads.map((h, i) => {
                const icon = st.sortCol === i ? (st.sortDir === 'asc' ? '↑' : '↓') : '↕';
                return `<th data-col="${i}" class="${i === 0 ? 'text-start' : ''}">${esc(h)}<span class="sort">${icon}</span></th>`;
            }).join('');

            // body
            tbody.innerHTML = slice.map(r => {
                const tds = r.cells.map((c, i) =>
                    `<td class="${typeof c === 'number' ? 'text-end' : ''}">${fmt(c)}</td>`).join('');
                return `<tr><td class="no">${r.no}</td>${tds}</tr>`;
            }).join('');

            // info
            const dari = total === 0 ? 0 : start + 1;
            const sampai = Math.min(start + PER_PAGE, total);
            info.textContent = `Showing ${dari} to ${sampai} of ${total} entries`;

            renderPagination(pages, st.page);

            // listener sort
            thead.querySelectorAll('th').forEach(th => th.addEventListener('click', () => {
                const col = parseInt(th.dataset.col, 10);
                if (st.sortCol === col) st.sortDir = st.sortDir === 'asc' ? 'desc' : 'asc';
                else { st.sortCol = col; st.sortDir = 'asc'; }
                st.page = 1;
                render();
            }));
        }

        function renderPagination(pages, current) {
            const items = [];
            items.push({ label: 'Previous', page: current - 1, disabled: current === 1 });

            const nums = [];
            if (pages <= 7) {
                for (let i = 1; i <= pages; i++) nums.push(i);
            } else {
                nums.push(1);
                const from = Math.max(2, current - 1);
                const to   = Math.min(pages - 1, current + 1);
                if (from > 2) nums.push('...');
                for (let i = from; i <= to; i++) nums.push(i);
                if (to < pages - 1) nums.push('...');
                nums.push(pages);
            }
            nums.forEach(n => {
                if (n === '...') items.push({ label: '…', disabled: true });
                else items.push({ label: n, page: n, active: n === current });
            });

            items.push({ label: 'Next', page: current + 1, disabled: current === pages });

            pag.innerHTML = items.map(it => `
                <li class="page-item ${it.active ? 'active' : ''} ${it.disabled ? 'disabled' : ''}">
                    <a class="page-link" href="#" ${it.page ? `data-page="${it.page}"` : ''}>${it.label}</a>
                </li>`).join('');

            pag.querySelectorAll('a[data-page]').forEach(a => a.addEventListener('click', e => {
                e.preventDefault();
                state[tabAktif].page = parseInt(a.dataset.page, 10);
                render();
            }));
        }

        // ganti tab
        document.querySelectorAll('#tabButtons .btn-tab').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#tabButtons .btn-tab').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                tabAktif = this.dataset.tab;
                render();
            });
        });

        render();
    });
</script>
@endsection