@extends('layouts.app')

@section('content')

<style>
    .rs-card { border-radius: 1.5rem; overflow: hidden; }
    .rs-photo { aspect-ratio: 1 / .88; overflow: hidden; background: #f8fafc; border-radius: 1.5rem; }
    .rs-photo img { width: 100%; height: 100%; object-fit: cover; }
    .rs-info dt { color: #64748b; font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; }
    .rs-info dd { margin-bottom: 1rem; }
    .btn-tab { min-width: 130px; }
    .tbl-rs thead th { background: #f1f5f9; cursor: pointer; font-size: .85rem; user-select: none; white-space: nowrap; }
    .tbl-rs thead th .sort { color: #94a3b8; font-size: .7rem; margin-left: .35rem; }
    .tbl-rs tbody td { font-size: .85rem; padding-bottom: .8rem; padding-top: .8rem; }
    .tbl-rs td.no { text-align: right; width: 90px; }
</style>

<div class="container-fluid py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h3 class="fw-bold mb-1">Profil Rumah Sakit</h3>
            <div class="text-muted small">Informasi profil, layanan, fasilitas, dan tenaga medis</div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Kartu profil --}}
        <div class="col-lg-4">
            <div class="card rs-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="rs-photo rounded-3 mb-3">
                        @if ($rs['foto'])
                            <img src="{{ $rs['foto'] }}" alt="{{ $rs['nama'] }}">
                        @else
                            <div class="d-flex h-100 align-items-center justify-content-center text-muted small">
                                Foto belum tersedia
                            </div>
                        @endif
                    </div>

                    <h4 class="fw-bold text-center mb-4">{{ $rs['nama'] }}</h4>

                    <dl class="rs-info mb-0">
                        <dt>Alamat</dt>
                        <dd>{{ $rs['alamat'] }}</dd>

                        <dt>Telepon</dt>
                        <dd>{{ $rs['telepon'] }}</dd>

                        <dt>Jenis Rumah Sakit</dt>
                        <dd>{{ $rs['jenis'] }}</dd>

                        <dt>Kelas Rumah Sakit</dt>
                        <dd>{{ $rs['kelas'] }}</dd>

                        <dt>Kepemilikan</dt>
                        <dd class="mb-0">{{ $rs['kepemilikan'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Tab + tabel --}}
        <div class="col-lg-8">
            <div class="card rs-card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 p-3">
                    <div class="d-flex flex-wrap gap-2 pt-3" id="tabButtons">
                        <button type="button" class="btn btn-primary btn-sm btn-tab active" data-tab="fasilitas">Fasilitas</button>
                        <button type="button" class="btn btn-outline-primary btn-sm btn-tab" data-tab="layanan">Layanan</button>
                        <button type="button" class="btn btn-outline-primary btn-sm btn-tab" data-tab="tenaga">Tenaga Medis</button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive rounded border">
                        <table class="table table-hover align-middle tbl-rs mb-0">
                            <thead><tr id="theadRow"></tr></thead>
                            <tbody id="tbodyRows"></tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-2">
                        <div class="small text-muted" id="infoEntries"></div>
                        <nav aria-label="Navigasi tabel">
                            <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = @json($tabs);
        const PER_PAGE = 10;

        let tabAktif = 'fasilitas';
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
                    document.querySelectorAll('#tabButtons .btn-tab').forEach(b => {
                        b.classList.remove('active', 'btn-primary');
                        b.classList.add('btn-outline-primary');
                    });
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('active', 'btn-primary');
                    tabAktif = this.dataset.tab;
                    render();
                });
            });

        render();
    });
</script>
@endsection