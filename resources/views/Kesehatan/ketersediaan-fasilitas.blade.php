@extends('layouts.app')

@section('content')
@php
    // =====================================================================
    // DATA STATIS
    // =====================================================================
    $tahunList  = [2026, 2025, 2024];
    $tahunAktif = 2026;

    // Rata-rata skor provinsi (sesuai mockup)
    $skorRata = 68.4;

    // Kategori indeks (sesuai legenda peta): satu-satunya tempat aturan ambang skor
    $kategori = [
        ['min' => 80, 'label' => 'Sangat Baik',   'teks' => '#0f9d58', 'chip' => '#16a870', 'chipTeks' => '#ffffff'],
        ['min' => 60, 'label' => 'Baik',          'teks' => '#43a047', 'chip' => '#9ad65f', 'chipTeks' => '#1b3a12'],
        ['min' => 40, 'label' => 'Cukup',         'teks' => '#f59e0b', 'chip' => '#f8d65a', 'chipTeks' => '#5b3d00'],
        ['min' => 20, 'label' => 'Kurang',        'teks' => '#f97316', 'chip' => '#f99a6e', 'chipTeks' => '#ffffff'],
        ['min' => 0,  'label' => 'Sangat Kurang', 'teks' => '#ef4444', 'chip' => '#f25f5c', 'chipTeks' => '#ffffff'],
    ];
    $cariKategori = function ($skor) use ($kategori) {
        foreach ($kategori as $k) {
            if ($skor >= $k['min']) return $k;
        }
        return end($kategori);
    };

    // nama, skor, targetRS, aktualRS, targetPuskesmas, aktualPuskesmas, prioritas
    $wilayah = [
        ['Banda Aceh',      92.3, 15, 14, 50, 48, 'Tinggi'],
        ['Lhokseumawe',     81.7,  6,  5, 25, 22, 'Sedang'],
        ['Aceh Besar',      76.5, 12, 10, 40, 36, 'Tinggi'],
        ['Langsa',          71.2,  5,  3, 20, 17, 'Sedang'],
        ['Pidie',           66.8,  8,  6, 30, 26, 'Tinggi'],
        ['Bireuen',         62.4,  7,  5, 28, 24, 'Sedang'],
        ['Aceh Utara',      58.9,  9,  7, 35, 31, 'Sedang'],
        ['Aceh Timur',      56.3,  7,  5, 28, 24, 'Sedang'],
        ['Aceh Tengah',     54.8,  6,  4, 22, 18, 'Sedang'],
        ['Aceh Barat',      53.1,  6,  4, 21, 17, 'Sedang'],
        ['Pidie Jaya',      51.7,  5,  3, 18, 14, 'Sedang'],
        ['Aceh Selatan',    50.2,  5,  3, 20, 16, 'Sedang'],
        ['Aceh Tamiang',    48.9,  5,  3, 19, 15, 'Sedang'],
        ['Nagan Raya',      47.6,  4,  3, 16, 13, 'Sedang'],
        ['Sabang',          46.9,  3,  2,  8,  6, 'Rendah'],
        ['Subulussalam',    45.6,  4,  2, 15, 12, 'Rendah'],
        ['Bener Meriah',    43.8,  3,  2, 14, 11, 'Rendah'],
        ['Aceh Jaya',       41.2,  3,  2, 12,  9, 'Rendah'],
        ['Aceh Barat Daya', 39.5,  3,  2, 13, 10, 'Rendah'],
        ['Aceh Tenggara',   37.9,  3,  2, 14, 11, 'Rendah'],
        ['Gayo Lues',       35.4,  2,  1, 11,  8, 'Rendah'],
        ['Aceh Singkil',    33.6,  2,  1, 12,  9, 'Rendah'],
        ['Simeulue',        31.7,  2,  1, 10,  7, 'Rendah'],
    ];

    $data = array_map(fn($w) => [
        'nama' => $w[0], 'skor' => $w[1],
        'rsTarget' => $w[2], 'rsAktual' => $w[3],
        'pkTarget' => $w[4], 'pkAktual' => $w[5],
        'prioritas' => $w[6],
        'rsKurang' => max(0, $w[2] - $w[3]),
        'pkKurang' => max(0, $w[4] - $w[5]),
    ], $wilayah);

    // Peringkat (berdasarkan skor, tertinggi dulu)
    usort($data, fn($a, $b) => $b['skor'] <=> $a['skor']);
    $peringkat = [];
    foreach ($data as $i => $d) {
        $k = $cariKategori($d['skor']);
        $peringkat[] = [
            'rank' => $i + 1, 'nama' => $d['nama'], 'skor' => $d['skor'],
            'skorTeks' => number_format($d['skor'], 1, ',', '.'),
            'rs' => $d['rsAktual'], 'pk' => $d['pkAktual'],
            'kategori' => $k['label'], 'warnaTeks' => $k['teks'],
            'chip' => $k['chip'], 'chipTeks' => $k['chipTeks'],
        ];
    }

    $tertinggi = $data[0];
    $terendah  = $data[count($data) - 1];
    $kategoriRata = $cariKategori($skorRata);

    // Wilayah yang ditampilkan pada grafik target vs aktual
    $wilayahGrafik = ['Banda Aceh', 'Aceh Besar', 'Pidie', 'Bireuen', 'Aceh Utara', 'Lhokseumawe', 'Langsa', 'Subulussalam', 'Simeulue'];
    $byNama = collect($data)->keyBy('nama');
    $grafik = [
        'kategori' => $wilayahGrafik,
        'rsTarget' => array_map(fn($n) => $byNama[$n]['rsTarget'], $wilayahGrafik),
        'rsAktual' => array_map(fn($n) => $byNama[$n]['rsAktual'], $wilayahGrafik),
        'pkTarget' => array_map(fn($n) => $byNama[$n]['pkTarget'], $wilayahGrafik),
        'pkAktual' => array_map(fn($n) => $byNama[$n]['pkAktual'], $wilayahGrafik),
    ];

    // Tabel kesenjangan: urut prioritas (Tinggi > Sedang > Rendah), lalu total kekurangan terbesar
    $urutPrioritas = ['Tinggi' => 0, 'Sedang' => 1, 'Rendah' => 2];
    $kesenjangan = $data;
    usort($kesenjangan, function ($a, $b) use ($urutPrioritas) {
        return [$urutPrioritas[$a['prioritas']], -($a['rsKurang'] + $a['pkKurang']), $a['nama']]
           <=> [$urutPrioritas[$b['prioritas']], -($b['rsKurang'] + $b['pkKurang']), $b['nama']];
    });

    $fmt1 = fn($n) => number_format($n, 1, ',', '.');
@endphp

<style>
    .dw-card { border: 1px solid #d9dee7; border-radius: .75rem; background: #fff; }
    .sec-title { font-weight: 700; font-size: 1.3rem; margin: 1.75rem 0 .75rem; }
    #map { height: 300px; background: #f8fafc; border-radius: .6rem; }

    .skor-box { text-align: center; padding: .9rem .5rem; }
    .skor-box .lbl { font-size: .75rem; font-weight: 600; color: #1e293b; }
    .skor-box .val { font-size: 2rem; font-weight: 800; color: #0f172a; line-height: 1.2; margin: .35rem 0; }
    .skor-box .wil { font-size: .75rem; font-weight: 600; color: #1e293b; }

    .chart-card { border: 1px solid #e5e7eb; border-radius: .6rem; background: #fff; padding: .8rem 1rem; box-shadow: 0 1px 5px rgba(15, 23, 42, .06); }
    .chart-card .judul { font-size: .8rem; font-weight: 700; color: #1e3a5f; }

    .tbl-wrap { background: #fff; border: 1px solid #e5e7eb; border-radius: .6rem; box-shadow: 0 1px 5px rgba(15, 23, 42, .06); overflow: hidden; }
    .tbl-rank thead th { background: #e8eff9; color: #1e3a5f; font-size: .82rem; font-weight: 700; text-align: center; vertical-align: middle; padding: .8rem .5rem; white-space: nowrap; }
    .tbl-rank tbody td { font-size: .85rem; text-align: center; vertical-align: middle; padding: .6rem .5rem; }
    .tbl-rank tbody td.nama { text-align: left; }
    .tbl-rank th.sortable { cursor: pointer; user-select: none; }
    .skor-chip { display: inline-block; min-width: 56px; padding: .2rem .55rem; border-radius: .35rem; font-weight: 700; }
    .kat { font-weight: 700; }

    .pager { display: flex; justify-content: space-between; align-items: center; padding: .8rem 1rem; font-size: .8rem; color: #64748b; }
    .pager .page-link { border-radius: .4rem; margin: 0 .15rem; font-size: .8rem; min-width: 32px; text-align: center; }

    .kes-scroll { max-height: 440px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #c3c8d0 transparent; }
    .kes-scroll::-webkit-scrollbar { width: 8px; }
    .kes-scroll::-webkit-scrollbar-thumb { background: #c3c8d0; border-radius: 8px; }
    .tbl-kes { margin-bottom: 0; }
    .tbl-kes thead th { background: #e8eff9; color: #1e3a5f; font-size: .78rem; font-weight: 700; text-align: center; vertical-align: middle; position: sticky; z-index: 2; height: 36px; padding: .35rem .5rem; }
    .tbl-kes thead tr:first-child th { top: 0; }
    .tbl-kes thead tr:nth-child(2) th { top: 36px; }
    .tbl-kes tbody td { font-size: .8rem; text-align: center; vertical-align: middle; padding: .35rem .5rem; }
    .tbl-kes tbody td.nama { text-align: left; }
    .kurang { color: #dc2626; font-weight: 700; }
    .prio { display: inline-block; min-width: 70px; padding: .15rem .7rem; border-radius: 999px; font-size: .75rem; font-weight: 700; }
    .prio.Tinggi { background: #ef4444; color: #fff; }
    .prio.Sedang { background: #fde68a; color: #92400e; }
    .prio.Rendah { background: #bbf7d0; color: #166534; }
</style>

<div class="container-fluid py-3">

    {{-- Breadcrumb + filter tahun --}}
    <div class="d-flex justify-content-between align-items-start mb-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Kesehatan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ketersediaan Layanan</li>
            </ol>
        </nav>
        <select id="filterTahun" class="form-select form-select-sm w-auto">
            @foreach ($tahunList as $t)
                <option value="{{ $t }}" {{ $t == $tahunAktif ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
    </div>

    <h3 class="fw-bold mb-3">Peta Sebaran Indeks Ketersediaan Fasilitas</h3>

    {{-- Peta + skor --}}
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="dw-card p-2 h-100">
                {{-- Bagian peta dikosongkan (siapkan untuk Leaflet choropleth) --}}
                <div id="map"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3 h-100">
                <div class="dw-card p-3 flex-grow-1">
                    <div class="fw-semibold small">Rata-rata Skor Provinsi Aceh</div>
                    <div id="chartGauge"></div>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="dw-card skor-box h-100">
                            <div class="lbl">Skor Tertinggi</div>
                            <div class="val">{{ $fmt1($tertinggi['skor']) }}</div>
                            <div class="wil">{{ $tertinggi['nama'] }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="dw-card skor-box h-100">
                            <div class="lbl">Skor Terendah</div>
                            <div class="val">{{ $fmt1($terendah['skor']) }}</div>
                            <div class="wil">{{ $terendah['nama'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Perbandingan target dan realisasi --}}
    <div class="sec-title">Perbandingan Target dan Realisasi Fasilitas Kesehatan</div>
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="chart-card h-100">
                <div class="judul">Rumah Sakit (RS)</div>
                <div id="chartRs"></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-card h-100">
                <div class="judul">Puskesmas</div>
                <div id="chartPuskesmas"></div>
            </div>
        </div>
    </div>

    {{-- Peringkat --}}
    <div class="sec-title">Peringkat Kabupaten/Kota Berdasarkan Ketersediaan Fasilitas</div>
    <div class="tbl-wrap">
        <div class="table-responsive">
            <table class="table tbl-rank mb-0">
                <thead>
                    <tr>
                        <th>Peringkat</th>
                        <th class="text-start">Kabupaten/Kota</th>
                        <th class="sortable" id="thSkor">Skor <span id="iconSkor">&#9670;</span></th>
                        <th>Jumlah RS</th>
                        <th>Jumlah Puskesmas</th>
                        <th>Kategori</th>
                    </tr>
                </thead>
                <tbody id="tbodyRank"></tbody>
            </table>
        </div>
        <div class="pager">
            <div id="infoRank"></div>
            <ul class="pagination pagination-sm mb-0" id="pagRank"></ul>
        </div>
    </div>

    {{-- Analisis kesenjangan --}}
    <div class="sec-title">Analisis Kesenjangan dan Prioritas Pembangunan Fasilitas</div>
    <div class="tbl-wrap mb-4">
        <div class="px-3 pt-3 pb-2 fw-bold" style="color:#1e3a5f; font-size:.9rem;">Kesenjangan Fasilitas per Kabupaten/Kota</div>
        <div class="kes-scroll">
            <table class="table tbl-kes">
                <thead>
                    <tr>
                        <th rowspan="2" style="min-width:140px">Kabupaten/Kota</th>
                        <th colspan="3">Kebutuhan RS</th>
                        <th colspan="3">Kebutuhan Puskesmas</th>
                        <th rowspan="2">Prioritas</th>
                    </tr>
                    <tr>
                        <th>Target</th><th>Aktual</th><th>Kekurangan</th>
                        <th>Target</th><th>Aktual</th><th>Kekurangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kesenjangan as $k)
                        <tr>
                            <td class="nama">{{ $k['nama'] }}</td>
                            <td>{{ $k['rsTarget'] }}</td>
                            <td>{{ $k['rsAktual'] }}</td>
                            <td class="kurang">{{ $k['rsKurang'] }}</td>
                            <td>{{ $k['pkTarget'] }}</td>
                            <td>{{ $k['pkAktual'] }}</td>
                            <td class="kurang">{{ $k['pkKurang'] }}</td>
                            <td><span class="prio {{ $k['prioritas'] }}">{{ $k['prioritas'] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ApexCharts (hapus jika layouts.app sudah memuatnya) --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const skorRata     = {{ $skorRata }};
        const skorRataTeks = @json($fmt1($skorRata));
        const kategoriRata = @json($kategoriRata['label']);
        const warnaRata    = @json($kategoriRata['teks']);
        const grafik       = @json($grafik);
        const peringkat    = @json($peringkat);

        // ---------- Gauge rata-rata skor ----------
        new ApexCharts(document.querySelector('#chartGauge'), {
            chart: { type: 'radialBar', height: 190, sparkline: { enabled: true } },
            series: [skorRata],
            labels: [kategoriRata],
            plotOptions: {
                radialBar: {
                    hollow: { size: '66%' },
                    track: { background: '#e3edf9', strokeWidth: '100%' },
                    dataLabels: {
                        name: { show: true, offsetY: 22, fontSize: '12px', fontWeight: 700, color: warnaRata },
                        value: { show: true, offsetY: -14, fontSize: '28px', fontWeight: 800, color: '#0f172a', formatter: () => skorRataTeks }
                    }
                }
            },
            fill: { type: 'gradient', gradient: { shade: 'light', type: 'horizontal', gradientToColors: ['#0f9d8a'], stops: [0, 100] } },
            colors: ['#9ad65f'],
            stroke: { lineCap: 'round' }
        }).render();

        // ---------- Grafik target vs aktual ----------
        function grouped(sel, target, aktual, maks) {
            return new ApexCharts(document.querySelector(sel), {
                chart: { type: 'bar', height: 300, toolbar: { show: false } },
                series: [
                    { name: 'Target', data: target },
                    { name: 'Aktual', data: aktual }
                ],
                xaxis: { categories: grafik.kategori, labels: { rotate: -60, rotateAlways: true, style: { fontSize: '10px' }, trim: false } },
                yaxis: { max: maks, labels: { style: { fontSize: '10px' } } },
                plotOptions: { bar: { columnWidth: '65%', borderRadius: 2, dataLabels: { position: 'top' } } },
                colors: ['#2f80ed', '#10b981'],
                dataLabels: { enabled: true, offsetY: -18, style: { fontSize: '10px', colors: ['#334155'] } },
                legend: { position: 'top', horizontalAlign: 'left', fontSize: '11px', markers: { radius: 12 } },
                grid: { strokeDashArray: 3 }
            }).render();
        }
        grouped('#chartRs', grafik.rsTarget, grafik.rsAktual, 20);
        grouped('#chartPuskesmas', grafik.pkTarget, grafik.pkAktual, 60);

        // ---------- Tabel peringkat (urut skor + pagination) ----------
        const PER_PAGE = 10;
        let halaman = 1;
        let urutSkor = 'desc';   // 'desc' = peringkat 1 di atas

        const tbody = document.getElementById('tbodyRank');
        const info  = document.getElementById('infoRank');
        const pag   = document.getElementById('pagRank');
        const icon  = document.getElementById('iconSkor');

        function renderRank() {
            const rows = peringkat.slice().sort((a, b) => urutSkor === 'desc' ? b.skor - a.skor : a.skor - b.skor);
            const total = rows.length;
            const pages = Math.ceil(total / PER_PAGE);
            if (halaman > pages) halaman = pages;
            const start = (halaman - 1) * PER_PAGE;

            tbody.innerHTML = rows.slice(start, start + PER_PAGE).map(r => `
                <tr>
                    <td>${r.rank}</td>
                    <td class="nama">${r.nama}</td>
                    <td><span class="skor-chip" style="background:${r.chip};color:${r.chipTeks}">${r.skorTeks}</span></td>
                    <td>${r.rs}</td>
                    <td>${r.pk}</td>
                    <td><span class="kat" style="color:${r.warnaTeks}">${r.kategori}</span></td>
                </tr>`).join('');

            info.textContent = `Menampilkan ${start + 1}–${Math.min(start + PER_PAGE, total)} dari ${total} data`;
            icon.textContent = urutSkor === 'desc' ? '▼' : '▲';

            let html = `<li class="page-item ${halaman === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-p="${halaman - 1}">&lsaquo;</a></li>`;
            for (let i = 1; i <= pages; i++) {
                html += `<li class="page-item ${i === halaman ? 'active' : ''}"><a class="page-link" href="#" data-p="${i}">${i}</a></li>`;
            }
            html += `<li class="page-item ${halaman === pages ? 'disabled' : ''}"><a class="page-link" href="#" data-p="${halaman + 1}">&rsaquo;</a></li>`;
            pag.innerHTML = html;

            pag.querySelectorAll('a[data-p]').forEach(a => a.addEventListener('click', e => {
                e.preventDefault();
                halaman = parseInt(a.dataset.p, 10);
                renderRank();
            }));
        }

        document.getElementById('thSkor').addEventListener('click', () => {
            urutSkor = urutSkor === 'desc' ? 'asc' : 'desc';
            halaman = 1;
            renderRank();
        });

        renderRank();
    });
</script>
@endsection