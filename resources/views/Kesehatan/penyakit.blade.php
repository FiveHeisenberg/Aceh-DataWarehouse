@extends('layouts.app')

@section('content')
@php
    // helper format angka Indonesia (12.482)
    $fmt = fn($n) => number_format($n, 0, ',', '.');
@endphp

<style>
    .dw-card { border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; }
    .dw-kpi { display: flex; align-items: center; gap: .9rem; border: 1px solid #e5e7eb; border-radius: .75rem; background: #fff; padding: .9rem 1.1rem; }
    .dw-kpi .icon { width: 52px; height: 52px; flex: 0 0 52px; border-radius: .6rem; background: #f1f5f9; border: 1px solid #d1d5db; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; }
    .dw-kpi .title { font-weight: 600; font-size: .95rem; color: #1e293b; }
    .dw-kpi .value { font-weight: 800; font-size: 1.5rem; line-height: 1.1; color: #0f172a; }
    .dw-kpi .desc { font-size: .78rem; color: #475569; }
    #map { min-height: 100%; height: 100%; background: #f8fafc; border-radius: .6rem; }
    .map-wrap { height: 100%; min-height: 330px; }
    .bar-row { display: flex; align-items: center; gap: .6rem; margin-bottom: .45rem; font-size: .8rem; }
    .bar-row .name { width: 120px; flex: 0 0 120px; color: #1e293b; }
    .bar-row .track { flex: 1; height: 12px; background: #eef2f7; border-radius: 3px; overflow: hidden; }
    .bar-row .fill { height: 100%; background: #7a98c0; border-radius: 3px; }
    .bar-row .val { width: 48px; flex: 0 0 48px; text-align: right; color: #334155; font-variant-numeric: tabular-nums; }
    .legend-item { display: flex; align-items: center; font-size: .85rem; margin-bottom: .55rem; }
    .legend-dot { width: 12px; height: 12px; border-radius: 50%; margin-right: .6rem; flex: 0 0 12px; }
    .gender-box { display: flex; justify-content: space-around; border-top: 1px solid #e5e7eb; padding-top: .8rem; margin-top: .4rem; background: #f8fafc; border-radius: .5rem; padding-bottom: .6rem; }
    .gender-box .pct { font-weight: 700; font-size: 1.1rem; }
    .gender-box .lbl { font-size: .75rem; color: #64748b; }
    .table-summary thead th { background: #e5e7eb; text-align: center; font-weight: 700; }
    .table-summary tbody td { padding-top: .9rem; padding-bottom: .9rem; }
</style>

<div class="container-fluid py-3">

    {{-- Breadcrumb + filter tahun --}}
    <div class="d-flex justify-content-between align-items-start mb-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small mb-0">
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Home</a></li>
                <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-dark">Kesehatan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Penyakit</li>
            </ol>
        </nav>
        <form method="GET" action="{{ route('kesehatan.penyakit') }}">
            <label for="filterTahun" class="visually-hidden">Pilih tahun</label>
            <select
                id="filterTahun"
                name="tahun"
                class="form-select form-select-sm w-auto"
                onchange="this.form.submit()"
            >
                @foreach ($tahunList as $tahun)
                    <option value="{{ $tahun }}" @selected($tahun === $tahunAktif)>
                        {{ $tahun }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <h2 class="fw-bold mb-0">Analisis Penyakit</h2>
    <p class="mb-3">Ringkasan dan persebaran data penyakit di Aceh</p>

    {{-- Peta + KPI --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="dw-card p-2 map-wrap">
                {{-- Bagian peta  --}}
                <link
                    rel="stylesheet"
                    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
                />

                <meta
                    name="geojson-url"
                    content="{{ asset('js/Penduduk/aceh-kabupaten.geojson') }}"
                >

                <script type="application/json" id="data-peta-kasus">
                    @json($petaKasus)
                </script>
                <div id="map"></div>
            </div>
        </div>
        <div class="col-lg-4 d-flex flex-column gap-3">
            <div class="dw-kpi">
                <div class="icon"><i class="bi bi-emoji-dizzy"></i></div>
                <div>
                    <div class="title">Total Kasus Penyakit</div>
                    <div class="value">{{ $fmt($totalKasus) }} </div>
                    <div class="desc">dari seluruh kabupaten/kota</div>
                </div>
            </div>
            <div class="dw-kpi">
                <div class="icon"><i class="bi bi-virus"></i></div>
                <div>
                    <div class="title">Penyakit Terbanyak</div>
                    <div class="value">{{ $penyakitTerbanyak->nama ?? '-' }}</div>
                    <div class="desc">{{ $fmt((int) ($penyakitTerbanyak->jumlah ?? 0)) }} kasus</div>
                </div>
            </div>
            <div class="dw-kpi">
                <div class="icon"><i class="bi bi-emoji-dizzy"></i></div>
                <div>
                    <div class="title">Kabupaten/Kota Terbanyak</div>
                    <div class="value">{{ $kabupatenTerbanyak->nama ?? '-' }}</div>
                    <div class="desc">{{ $kabupatenTerbanyak->jumlah ?? 0 }} Kasus</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 10 penyakit terbanyak & kasus per kabupaten --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <h6 class="fw-bold mb-3">10 Penyakit Terbanyak</h6>
                @foreach ($penyakit as $p)
                    <div class="bar-row">
                        <div class="name">{{ $p['nama'] }}</div>
                        <div class="track"><div class="fill" style="width: {{ $maxPenyakit > 0 ? round(($p['jumlah'] / $maxPenyakit) * 100, 1) : 0 }}%"></div></div>
                        <div class="val">{{ $fmt($p['jumlah']) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <h6 class="fw-bold mb-3">Kasus Penyakit per Kabupaten/Kota</h6>
                @foreach ($kabupaten as $i => $k)
                    <div class="bar-row">
                        <div class="name">{{ $i + 1 }}. {{ $k['nama'] }}</div>
                        <div class="track"><div class="fill" style="width: {{ $maxKabupaten > 0 ? round(($k['jumlah'] / $maxKabupaten) * 100, 1) : 0 }}%"></div></div>
                        <div class="val">{{ $fmt($k['jumlah']) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Distribusi usia & jenis kelamin --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="dw-card p-3 h-100">
                <h6 class="fw-bold mb-3">Distribusi Kasus berdasarkan Kelompok Usia</h6>
                <div class="text-center">
                    <div id="chartUsia"></div>
                </div>

                <div class="row g-2 mt-2">
                    @foreach ($usia as $u)
                    <div class="col-12 col-sm-6">
                        <div class="legend-item mb-0">
                            <span class="legend-dot" style="background: {{ $u['warna'] }}"></span>
                            <span class="flex-grow-1">{{ $u['label'] }}</span>
                            <span class="text-nowrap">
                                {{ $fmt($u['jumlah']) }} ({{ $u['persen'] }})
                            </span>
                        </div>
                    </div>
                    @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="dw-card p-3 h-100">
                    <h6 class="fw-bold mb-2">Kasus berdasarkan Jenis Kelamin</h6>
                    <div id="chartGender"></div>
                    <div class="gender-box">
                        <div class="text-center">
                            <i class="bi bi-person-standing text-primary fs-4"></i>
                            <div class="pct">{{ $gender['laki']['persen'] }}</div>
                            <div class="lbl">Laki-laki</div>
                        </div>
                        <div class="text-center">
                            <i class="bi bi-person-standing-dress fs-4"></i>
                            <div class="pct">{{ $gender['perempuan']['persen'] }}</div>
                            <div class="lbl">Perempuan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan persebaran penyakit --}}
    <h4 class="fw-bold mb-3">Ringkasan Persebaran Penyakit per Kabupaten/Kota</h4>
    <div class="table-responsive mb-4">
        <table class="table table-summary align-middle mb-0">
            <thead>
                <tr>
                    <th>Kabupaten/Kota</th>
                    <th>Total Kasus</th>
                    <th>Penyakit Tertinggi</th>
                    <th>Jumlah Kasus Tertinggi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ringkasan as $r)
                    <tr>
                        <td>{{ $r['kabupaten'] }}</td>
                        <td class="text-center">{{ $fmt($r['jumlah']) }}</td>
                        <td>{{ $r['penyakit_tertinggi'] }}</td>
                        <td class="text-center">{{ $fmt($r['jumlah_penyakit_tertinggi']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Bootstrap Icons & ApexCharts (hapus jika layouts.app sudah memuatnya) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.1/dist/apexcharts.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dataUsia = @json($usia);
        const total    = {{ $totalKasus }};
        const rupiah   = n => n.toLocaleString('id-ID');

        // ---------- Donut kelompok usia ----------
        new ApexCharts(document.querySelector('#chartUsia'), {
            chart: {
                type: 'donut',
                height: 240,
                width: '100%',
                parentHeightOffset: 0
            },
            plotOptions: {
                pie: {
                    customScale: 0.9,
                    donut: {
                        size: '58%',
                        labels: {
                            show: true,
                            name: { show: false },
                            value: {
                                show: true,
                                fontSize: '20px',
                                fontWeight: 700,
                                offsetY: 4
                            },
                            total: {
                                show: true,
                                label: 'kasus',
                                formatter: () => rupiah(total)
                            }
                        }
                    }
                }
            },
            series: dataUsia.map(d => d.jumlah),
            labels: dataUsia.map(d => d.label),
            colors: dataUsia.map(d => d.warna),
            legend: { show: false },
            dataLabels: {
                enabled: true,
                formatter: v => v.toFixed(1).replace('.', ',') + '%'
            },
            tooltip: {
                y: { formatter: v => rupiah(v) + ' kasus' }
            }
        }).render();

        // ---------- Bar jenis kelamin ----------
        const genderValues = [
            {{ $gender['laki']['jumlah'] }},
            {{ $gender['perempuan']['jumlah'] }}
        ];

        const genderMax = Math.max(...genderValues, 0);
        const genderAxisMax = genderMax > 0
            ? Math.ceil(genderMax * 1.2 / 1000) * 1000
            : 1000;
        
        new ApexCharts(document.querySelector('#chartGender'), {
            chart: {
                type: 'bar',
                height: 240,
                toolbar: { show: false }
            },
            series: [{
                name: 'Kasus',
                data: genderValues
            }],
            xaxis: {
                categories: ['Laki-laki', 'Perempuan']
            },
            yaxis: {
                min: 0,
                max: genderAxisMax,
                tickAmount: 4,
                labels: {
                    formatter: v => rupiah(v)
                }
            },
            plotOptions: {
                bar: {
                    distributed: true,
                    borderRadius: 4,
                    columnWidth: '40%'
                }
            },
            colors: ['#3b82f6', '#22a06b'],
            legend: { show: false },
            dataLabels: {
                enabled: true,
                formatter: v => rupiah(v),
                offsetY: -18,
                style: { colors: ['#0f172a'] }
            },
            tooltip: {
                y: { formatter: v => rupiah(v) + ' kasus' }
            }
        }).render();

    });

    document.addEventListener('DOMContentLoaded', async function () {
        if (typeof L === 'undefined') {
            return;
        }

        const mapElement = document.getElementById('map');
        const geojsonUrl = document.querySelector('meta[name="geojson-url"]')?.content;
        const dataElement = document.getElementById('data-peta-kasus');

        if (!mapElement || !geojsonUrl || !dataElement) {
            return;
        }

        const dataPeta = JSON.parse(dataElement.textContent || '[]');

        const normalisasiNama = (nama) => String(nama || '')
            .replace(/^(Kabupaten|Kota)\s+/i, '')
            .trim()
            .toLowerCase();

        const formatAngka = (angka) => Number(angka || 0)
            .toLocaleString('id-ID');

        const dataByKabupaten = new Map(
            dataPeta.map((item) => [
                normalisasiNama(item.kabupaten),
                item
            ])
        );

        const nilaiKasus = dataPeta.map((item) => Number(item.jumlah || 0));
        const kasusMaksimum = Math.max(...nilaiKasus, 0);

        const warnaKasus = (jumlah) => {
            if (kasusMaksimum === 0) {
                return '#e2e8f0';
            }

            const rasio = jumlah / kasusMaksimum;

            if (rasio > 0.75) return '#991b1b';
            if (rasio > 0.50) return '#dc2626';
            if (rasio > 0.25) return '#f97316';
            if (rasio > 0.10) return '#facc15';

            return '#bbf7d0';
        };

        const map = L.map(mapElement, {
            scrollWheelZoom: false
        });

        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                attribution: '&copy; OpenStreetMap contributors'
            }
        ).addTo(map);

        const geojson = await fetch(geojsonUrl)
            .then((response) => {
                if (!response.ok) {
                    throw new Error('GeoJSON gagal dimuat');
                }

                return response.json();
            });

        const geoLayer = L.geoJSON(geojson, {
            style: function (feature) {
                const nama = feature.properties?.nama || '';
                const data = dataByKabupaten.get(normalisasiNama(nama));

                return {
                    color: '#ffffff',
                    weight: 1.5,
                    fillColor: warnaKasus(Number(data?.jumlah || 0)),
                    fillOpacity: 0.8
                };
            },
                
            onEachFeature: function (feature, layer) {
                const nama = feature.properties?.nama || '-';
                const data = dataByKabupaten.get(normalisasiNama(nama));

                const jumlah = Number(data?.jumlah || 0);
                const penyakit = data?.penyakit_tertinggi || '-';
                const jumlahPenyakit = Number(
                    data?.jumlah_penyakit_tertinggi || 0
                );

                layer.bindTooltip(`
                    <strong>${nama}</strong><br>
                    Jumlah kasus: ${formatAngka(jumlah)}<br>
                    Penyakit tertinggi: ${penyakit}<br>
                    Kasus penyakit: ${formatAngka(jumlahPenyakit)}
                `, {
                    sticky: true
                });

                layer.on({
                    mouseover: function (event) {
                        event.target.setStyle({
                            weight: 3,
                            color: '#0f172a',
                            fillOpacity: 0.95
                        });

                        event.target.bringToFront();
                    },

                    mouseout: function (event) {
                        geoLayer.resetStyle(event.target);
                    }
                });
            }
        }).addTo(map);

        const bounds = geoLayer.getBounds();

        map.fitBounds(bounds, {
            padding: [15, 15]
        });

        map.setZoom(map.getZoom() + 1);
    });

</script>

@endsection