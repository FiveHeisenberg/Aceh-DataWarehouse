<?php

namespace App\Http\Controllers\Kesehatan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class KesehatanController extends Controller
{

    // UNTUK DIAGRAM REKAPULASI RS PER KELAS
    private const URUT_KELAS = ['KR01', 'KR02', 'KR03', 'KR04', 'KR05', 'KR06'];

    private const WARNA_KELAS = [
        'A' => '#3b82f6',
        'B' => '#14b8a6',
        'C' => '#f59e0b',
        'D' => '#8b5cf6',
        'D Pratama' => '#ef4444',
        'Belum Ditetapkan' => '#06b6d4',
    ];

    public function rumahSakit()
    {
        $db = DB::connection('mysql');

        $totalRs = (int) $db->table('dwh.dim_kesehatan_rs')->count();

        $kelas = $db->table('dwh.dim_kesehatan_rs as rs')
            ->leftJoin(
                'dwh.dim_kesehatan_wilayah as w',
                'rs.wilayah_key',
                '=',
                'w.wilayah_kesehatan_key'
            )
            ->select('rs.id_kelas_rs', 'rs.nama_kelas', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('rs.id_kelas_rs', 'rs.nama_kelas')
            ->get()
            ->sortBy(fn ($r) => $this->urutanKelas($r->id_kelas_rs))
            ->map(fn ($r) => [
                'label' => $r->nama_kelas ?: 'Belum Ditetapkan',
                'jumlah' => (int) $r->jumlah,
                'warna' => self::WARNA_KELAS[$r->nama_kelas] ?? '#64748b',
            ])
            ->values()
            ->all();
        
        $pemilik    = $this->rekap($db, 'nama_pemilik');
        $jenis      = $this->rekap($db, 'nama_jenis_rs');

        $daftarRs = $db->table('dwh.dim_kesehatan_rs as rs')
            ->leftJoin(
                'dwh.dim_kesehatan_wilayah as w',
                'rs.wilayah_key',
                '=',
                'w.wilayah_kesehatan_key'
            )
            ->orderBy('rs.nama_rs')
            ->get([
                'rs.rs_key', 'rs.id_rs', 'rs.nama_rs', 'rs.alamat', 'rs.telepon', 'rs.latitude', 'rs.longitude', 'rs.nama_kelas', 'rs.nama_jenis_rs', 'rs.nama_pemilik', 'w.nama_kabupaten_kota', 'w.nama_provinsi',
            ])
            ->map(fn ($r) => [
                'id'        => (int) $r->rs_key,
                'nama'      => $r->nama_rs,
                'provinsi'  => $r->nama_provinsi,
                'kota'      => $r->nama_kabupaten_kota,
                'alamat'    => $r->alamat,
                'telp'      => $r->telepon  ?: '-',
                'pemilik'  => $r->nama_pemilik,
                'kelas'    => $r->nama_kelas,
                'jenis'    => $r->nama_jenis_rs,
                'lat'      => $r->latitude === null ? null : (float) $r->latitude,
                'lng'      => $r->longitude === null ? null : (float) $r->longitude,
            ])
            ->all();
        
        return view('Kesehatan.rumah-sakit', compact('totalRs', 'kelas', 'pemilik', 'jenis', 'daftarRs'));
    }

    private function rekap($db, string $kolom): array
    {
        return $db->table('dwh.dim_kesehatan_rs as rs')
            ->select("rs.$kolom", DB::raw('COUNT(*) as jumlah'))
            ->groupBy("rs.$kolom")
            ->orderByDesc('jumlah')
            ->orderBy("rs.$kolom")
            ->get()
            ->map(fn ($r) => [
                'label' => $r->{$kolom} ?: '-',
                'jumlah' => (int) $r->jumlah,
            ])
            ->values()
            ->all();
    }

    private function urutanKelas(?string $id): int
    {
        $pos = array_search($id, self::URUT_KELAS, true);

        return $pos === false ? PHP_INT_MAX : $pos;
    }

    public function dataRs(int $rs)
    {
        $dataRs = DB::connection('mysql')
            ->table('dwh.dim_kesehatan_rs')
            ->where('rs_key', $rs)
            ->first([
                'rs_key',
                'nama_rs',
                'alamat',
                'telepon',
                'nama_jenis_rs',
                'nama_kelas',
                'nama_pemilik',
            ]);
        
        abort_if(!$dataRs, 404);

        $fasilitas = DB::connection('mysql')
            ->table('dwh.dim_kesehatan_fasilitas as f')
            ->join(
                'dwh.fact_kesehatan_fasilitas_rs as l',
                'f.fasilitas_key',
                '=',
                'l.fasilitas_key'
            )
            ->where('l.rs_key', $rs)
            ->orderBy('f.nama_fasilitas')
            ->get([
                'f.nama_fasilitas',
                'l.jumlah',
            ])
            ->map(fn ($item) => [
                $item->nama_fasilitas ?: '-',
                (int) ($item->jumlah ?? 0),
            ])
            ->all();

        $layanan = DB::connection('mysql')
            ->table('dwh.dim_kesehatan_layanan as f')
            ->join(
                'dwh.fact_kesehatan_layanan_rs as l',
                'f.layanan_key',
                '=',
                'l.layanan_key'
            )
            ->where('l.rs_key', $rs)
            ->orderBy('f.nama_layanan')
            ->get([
                'f.nama_layanan',
            ])
            ->map(fn ($item) => [
                $item->nama_layanan ?: '-',
            ])
            ->all();

        $tenagaMedis = DB::connection('mysql')
            ->table('dwh.fact_kesehatan_tenaga_medis as ftm')
            ->join(
                'dwh.dim_kesehatan_tenaga_medis as tm',
                'ftm.tenaga_key',
                '=',
                'tm.tenaga_key',
            )
            ->where('ftm.rs_key', $rs)
            ->orderBy('tm.nama_jenis_tenaga')
            ->get([
                'tm.nama_jenis_tenaga',
                'tm.nama_kelompok',
                'ftm.jumlah_tenaga',
            ])
            ->map(fn ($item) => [
                $item->nama_jenis_tenaga ?: '-',
                $item->nama_kelompok ?: '-',
                (int) ($item->jumlah_tenaga ?? 0),
            ])
            ->all();

        $rs = [
            'id'           => $dataRs->rs_key,
            'nama'         => $dataRs->nama_rs ?: '-',
            'alamat'       => $dataRs->alamat ?: '-',
            'telepon'      => $dataRs->telepon ?: '-',
            'jenis'        => $dataRs->nama_jenis_rs ?: '-',
            'kelas'        => $dataRs->nama_kelas ?: '-',
            'kepemilikan'  => $dataRs->nama_pemilik ?: '-',
            'foto'         => null,
        ];

        $tabs = [
            'fasilitas' => [
                'kolom' => ['Fasilitas', 'Jumlah'],
                'baris' => $fasilitas,
            ],
            'layanan' => [
                'kolom' => ['Pelayanan'],
                'baris' => $layanan,
            ],
            'tenaga' => [
                'kolom' => ['Nama Tenaga Medis', 'Jenis Tenaga Medis', 'Jumlah'],
                'baris' => $tenagaMedis,
            ],
        ];

            return view('kesehatan.data-rs', compact('rs', 'tabs'));
    }

    public function penyakit(Request $request)
    {
        $db = DB::connection('mysql');

        $tahunList = $db->table('dwh.fact_kesehatan_kasus_penyakit as fk')
            ->join('dwh.dim_waktu as dw', 'fk.waktu_key', '=', 'dw.waktu_key')
            ->select('dw.tahun')
            ->distinct()
            ->orderByDesc('dw.tahun')
            ->pluck('dw.tahun')
            ->map(fn ($tahun) => (int) $tahun)
            ->values()
            ->all();

        $tahunAktif = (int) ($request->input('tahun') ?: ($tahunList[0] ?? date('Y')));

        $base = $db->table('dwh.fact_kesehatan_kasus_penyakit as fk')
            ->join('dwh.dim_waktu as dw', 'fk.waktu_key', '=', 'dw.waktu_key')
            ->where('dw.tahun', $tahunAktif);

        $totalKasus = (int) (clone $base)
            ->sum('fk.jumlah_kasus');

        $penyakit = (clone $base)
            ->join(
                'dwh.dim_kesehatan_penyakit as dp',
                'fk.penyakit_key',
                '=',
                'dp.penyakit_key'
            )
            ->select(
                'dp.nama_penyakit as nama',
                DB::raw('SUM(fk.jumlah_kasus) as jumlah')
            )
            ->groupBy('dp.nama_penyakit')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get()
            ->map(fn ($item) => [
                'nama' => $item->nama ?: '-',
                'jumlah' => (int) $item->jumlah,
            ])
            ->all();
        
        $kabupaten = (clone $base)
            ->leftJoin(
                'dwh.dim_kesehatan_rs as rs',
                'fk.rs_key',
                '=',
                'rs.rs_key'
            )
            ->leftJoin('dwh.dim_kesehatan_wilayah as w', 'rs.wilayah_key', '=', 'w.wilayah_kesehatan_key')
            ->select(
                'w.nama_kabupaten_kota as nama',
                DB::raw('SUM(fk.jumlah_kasus) as jumlah')
            )
            ->whereNotNull('w.nama_kabupaten_kota')
            ->groupBy('w.nama_kabupaten_kota')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get()
            ->map(fn ($item) => [
                'nama' => $item->nama ?: '-',
                'jumlah' => (int) $item->jumlah,
            ])
            ->all();
        
        $maxPenyakit = max(array_column($penyakit, 'jumlah') ?: [1]);
        $maxKabupaten = max(array_column($kabupaten, 'jumlah') ?: [1]);

        $penyakitTerbanyak = (clone $base)
            ->join('dwh.dim_kesehatan_penyakit as dp', 'fk.penyakit_key', '=', 'dp.penyakit_key')
            ->select(
                'dp.nama_penyakit as nama',
                DB::raw('SUM(fk.jumlah_kasus) as jumlah')
            )
            ->groupBy('dp.nama_penyakit')
            ->orderByDesc('jumlah')
            ->first();

        $kabupatenTerbanyak = (clone $base)
            ->leftJoin('dwh.dim_kesehatan_rs as rs', 'fk.rs_key', '=', 'rs.rs_key')
            ->leftJoin('dwh.dim_kesehatan_wilayah as w', 'rs.wilayah_key', '=', 'w.wilayah_kesehatan_key')
            ->select(
                'w.nama_kabupaten_kota as nama',
                DB::raw('SUM(fk.jumlah_kasus) as jumlah')
            )
            ->groupBy('w.nama_kabupaten_kota')
            ->orderByDesc('jumlah')
            ->first();
        
        $usia = (clone $base)
            ->selectRaw("
                    CASE
                        WHEN fk.umur BETWEEN 0 AND 14 THEN '0 – 14 tahun'
                        WHEN fk.umur BETWEEN 15 AND 29 THEN '15 – 29 tahun'
                        WHEN fk.umur BETWEEN 30 AND 44 THEN '30 – 44 tahun'
                        WHEN fk.umur BETWEEN 45 AND 59 THEN '45 – 59 tahun'
                        WHEN fk.umur >= 60 THEN '60+ tahun'
                        ELSE 'Tidak diketahui'
                    END AS label,
                    SUM(fk.jumlah_kasus) AS jumlah
                ")
                ->groupByRaw("
                    CASE
                        WHEN fk.umur BETWEEN 0 AND 14 THEN '0 – 14 tahun'
                        WHEN fk.umur BETWEEN 15 AND 29 THEN '15 – 29 tahun'
                        WHEN fk.umur BETWEEN 30 AND 44 THEN '30 – 44 tahun'
                        WHEN fk.umur BETWEEN 45 AND 59 THEN '45 – 59 tahun'
                        WHEN fk.umur >= 60 THEN '60+ tahun'
                        ELSE 'Tidak diketahui'
                    END
                ")
            ->get()
            ->map(fn ($item) => [
                'label' => $item->label,
                'jumlah' => (int) $item->jumlah,
                'persen' => $totalKasus > 0
                    ? number_format(($item->jumlah / $totalKasus) * 100, 1, ',', '.') . '%'
                    : '0%',
                'warna' => match ($item->label) {
                    '0 – 14 tahun' => '#3b82f6',
                    '15 – 29 tahun' => '#22a06b',
                    '30 – 44 tahun' => '#f5b82e',
                    '45 – 59 tahun' => '#e8895a',
                    '60+ tahun' => '#8b7fd6',
                    default => '#94a3b8',
                },
            ])
            ->all();

        $genderRows = (clone $base)
            ->join(
                'dwh.dim_dukcapil_penduduk as pd',
                'fk.penduduk_key',
                '=',
                'pd.penduduk_key'
            )
            ->select(
                'pd.jenis_kelamin',
                DB::raw('SUM(fk.jumlah_kasus) AS jumlah')
            )
            ->whereIn('pd.jenis_kelamin', ['L', 'P'])
            ->groupBy('pd.jenis_kelamin')
            ->get()
            ->keyBy('jenis_kelamin');

        $laki = (int) ($genderRows->get('L')->jumlah ?? 0);
        $perempuan = (int) ($genderRows->get('P')->jumlah ?? 0);
        $totalGender = $laki + $perempuan;

        $gender = [
            'laki' => [
                'jumlah' => $laki,
                'persen' => $totalGender > 0
                    ? number_format(($laki / $totalGender) * 100, 1, ',', '.') . '%'
                    : '0%',
            ],
            'perempuan' => [
                'jumlah' => $perempuan,
                'persen' => $totalGender > 0
                    ? number_format(($perempuan / $totalGender) * 100, 1, ',', '.') . '%'
                    : '0%',
            ],
        ];
        
        $petaKasus = (clone $base)
            ->join(
                'dwh.dim_kesehatan_rs as rs',
                'fk.rs_key',
                '=',
                'rs.rs_key'
            )
            ->leftJoin(
                'dwh.dim_kesehatan_wilayah as w',
                'rs.wilayah_key',
                '=',
                'w.wilayah_kesehatan_key'
            )
            ->join(
                'dwh.dim_kesehatan_penyakit as dp',
                'fk.penyakit_key',
                '=',
                'dp.penyakit_key'
            )
            ->whereNotNull('w.nama_kabupaten_kota')
            ->select(
                'w.nama_kabupaten_kota as kabupaten', 'dp.nama_penyakit',
                DB::raw('SUM(fk.jumlah_kasus) as jumlah_penyakit')
            )
            ->groupBy(
                'w.nama_kabupaten_kota',
                'dp.nama_penyakit'
            )
            ->get()
            ->groupBy('kabupaten')
            ->map(function ($items, $kabupaten) {
                $items = $items
                    ->sortByDesc('jumlah_penyakit')
                    ->values();

                $penyakitTertinggi = $items->first();

                return [
                    'kabupaten' => $kabupaten,
                    'jumlah' => (int) $items->sum('jumlah_penyakit'),
                    'penyakit_tertinggi' => $penyakitTertinggi->nama_penyakit ?? '-',
                    'jumlah_penyakit_tertinggi' => (int) ($penyakitTertinggi->jumlah_penyakit ?? 0),
                ];
            })
            ->values()
            ->all();

        $ringkasan = collect($petaKasus)
            ->sortByDesc('jumlah')
            ->take(23)
            ->values()
            ->all();

        return view('Kesehatan.penyakit', [
            'tahunList' => $tahunList,
            'tahunAktif' => $tahunAktif,
            'totalKasus' => $totalKasus,
            'penyakit' => $penyakit,
            'kabupaten' => $kabupaten,
            'usia' => $usia,
            'gender' => $gender,
            'ringkasan' => $ringkasan,
            'maxPenyakit' => $maxPenyakit,
            'maxKabupaten' => $maxKabupaten,
            'penyakitTerbanyak' => $penyakitTerbanyak,
            'kabupatenTerbanyak' => $kabupatenTerbanyak,
            'petaKasus' => $petaKasus,
        ]);
    }

    public function tenagaMedis(Request $request)
    {
        $db = DB::connection('mysql');

        $tahunList = $db->table('dwh.fact_kesehatan_tenaga_medis as ftm')
            ->join('dwh.dim_waktu as dw', 'ftm.waktu_key', '=', 'dw.waktu_key')
            ->select('dw.tahun')
            ->distinct()
            ->orderByDesc('dw.tahun')
            ->pluck('dw.tahun')
            ->map(fn ($tahun) => (int) $tahun)
            ->values()
            ->all();

        $tahunAktif = (int) ($request->input('tahun') ?: ($tahunList[0] ?? date('Y')));

        if (! in_array($tahunAktif, $tahunList, true)) {
            $tahunAktif = $tahunList[0] ?? date('Y');
        }

        $base = $db->table('dwh.fact_kesehatan_tenaga_medis as ftm')
            ->join('dwh.dim_waktu as dw', 'ftm.waktu_key', '=', 'dw.waktu_key')
            ->where('dw.tahun', $tahunAktif);

        $totalTenaga = (int) (clone $base)
            ->sum('ftm.jumlah_tenaga');

        $jumlahKategori = (clone $base)
            ->join(
                'dwh.dim_kesehatan_tenaga_medis as tm',
                'ftm.tenaga_key',
                '=',
                'tm.tenaga_key'
            )
            ->select(
                'tm.kategori_profesi',
                DB::raw('SUM(ftm.jumlah_tenaga) as jumlah')
            )
            ->groupBy('tm.kategori_profesi')
            ->get()
            ->keyBy('kategori_profesi');

        $dokter = (int) ($jumlahKategori->get('Dokter')->jumlah ?? 0);
        $perawat = (int) ($jumlahKategori->get('Perawat')->jumlah ?? 0);
        $lainnya = (int) ($jumlahKategori->get('Lainnya')->jumlah ?? 0);

        $kpi = [
            [
                'judul' => 'Total Tenaga Medis',
                'nilai' => $totalTenaga,
                'ikon' => 'bi-people-fill',
                'warna' => '#3b82f6',
                'bg' => '#e8f1ff',
            ],
            [
                'judul' => 'Tenaga Medis Dokter',
                'nilai' => $dokter,
                'ikon' => 'bi-heart-pulse',
                'warna' => '#22a06b',
                'bg' => '#e6f6ee',
            ],
            [
                'judul' => 'Tenaga Medis Perawat',
                'nilai' => $perawat,
                'ikon' => 'bi-person-heart',
                'warna' => '#8b5cf6',
                'bg' => '#f0eaff',
            ],
            [
                'judul' => 'Tenaga Medis Lainnya',
                'nilai' => $lainnya,
                'ikon' => 'bi-person-badge',
                'warna' => '#14b8a6',
                'bg' => '#e3f7f4',
            ],
        ];

        $warnaKelompok = [
            'Perawat' => '#3b82f6',
            'Dokter Spesialis' => '#22a06b',
            'Dokter Umum' => '#8b7fd6',
            'Tenaga Kefarmasian' => '#f5b82e',
        ];

        $kelompok = (clone $base)
            ->join(
                'dwh.dim_kesehatan_tenaga_medis as tm',
                'ftm.tenaga_key',
                '=',
                'tm.tenaga_key'
            )
            ->select(
                'tm.nama_kelompok as label',
                DB::raw('SUM(ftm.jumlah_tenaga) as jumlah')
            )
            ->groupBy('tm.nama_kelompok')
            ->orderByDesc('jumlah')
            ->get()
            ->map(fn ($item) => [
                'label' => $item->label ?: '-',
                'jumlah' => (int) $item->jumlah,
                'warna' => $warnaKelompok[$item->label] ?? '#94a3b8',
            ])
            ->values()
            ->all();

        $jenis = (clone $base)
            ->join(
                'dwh.dim_kesehatan_tenaga_medis as tm',
                'ftm.tenaga_key',
                '=',
                'tm.tenaga_key'
            )
            ->select(
                'tm.nama_jenis_tenaga as nama',
                DB::raw('SUM(ftm.jumlah_tenaga) as jumlah')
            )
            ->groupBy('tm.nama_jenis_tenaga')
            ->orderByDesc('jumlah')
            ->get()
            ->map(fn ($item) => [
                'nama' => $item->nama ?: '-',
                'jumlah' => (int) $item->jumlah,
            ])
            ->values()
            ->all();

        $kabupaten = (clone $base)
            ->join(
                'dwh.dim_kesehatan_rs as rs',
                'ftm.rs_key',
                '=',
                'rs.rs_key'
            )
            ->leftJoin(
                'dwh.dim_kesehatan_wilayah as w',
                'rs.wilayah_key',
                '=',
                'w.wilayah_kesehatan_key'
            )
            ->select(
                'w.nama_kabupaten_kota as nama',
                DB::raw('SUM(ftm.jumlah_tenaga) as jumlah')
            )
            ->whereNotNull('w.nama_kabupaten_kota')
            ->groupBy('w.nama_kabupaten_kota')
            ->orderByDesc('jumlah')
            ->get()
            ->map(fn ($item) => [
                'nama' => $item->nama ?: '-',
                'jumlah' => (int) $item->jumlah,
            ])
            ->values()
            ->all();

        $maxJenis = max(array_column($jenis, 'jumlah') ?: [1]);
        $maxKabupaten = max(array_column($kabupaten, 'jumlah') ?: [1]);

        $profesiKabupaten = (clone $base)
            ->join(
                'dwh.dim_kesehatan_rs as rs',
                'ftm.rs_key',
                '=',
                'rs.rs_key'
            )
            ->leftJoin(
                'dwh.dim_kesehatan_wilayah as w',
                'rs.wilayah_key',
                '=',
                'w.wilayah_kesehatan_key'
            )
            ->join(
                'dwh.dim_kesehatan_tenaga_medis as tm',
                'ftm.tenaga_key',
                '=',
                'tm.tenaga_key'
            )
            ->whereNotNull('w.nama_kabupaten_kota')
            ->select(
                'w.nama_kabupaten_kota as kabupaten',
                DB::raw("
                    SUM(
                        CASE
                            WHEN tm.kategori_profesi = 'Dokter'
                            THEN ftm.jumlah_tenaga
                            ELSE 0
                        END
                    ) as dokter
                "),
                DB::raw("
                    SUM(
                        CASE
                            WHEN tm.kategori_profesi = 'Perawat'
                            THEN ftm.jumlah_tenaga
                            ELSE 0
                        END
                    ) as perawat
                "),
                DB::raw("
                    SUM(
                        CASE
                            WHEN tm.kategori_profesi = 'Lainnya'
                            THEN ftm.jumlah_tenaga
                            ELSE 0
                        END
                    ) as lainnya
                ")
            )
            ->groupBy('w.nama_kabupaten_kota')
            ->orderByDesc(
                DB::raw('SUM(ftm.jumlah_tenaga)')
            )
            ->get()
            ->map(fn ($item) => [
                'kabupaten' => $item->kabupaten ?: '-',
                'dokter' => (int) $item->dokter,
                'perawat' => (int) $item->perawat,
                'lainnya' => (int) $item->lainnya,
            ])
            ->values()
            ->all();

        return view('Kesehatan.tenaga-medis', compact(
            'tahunList',
            'tahunAktif',
            'kpi',
            'kelompok',
            'jenis',
            'kabupaten',
            'maxJenis',
            'maxKabupaten',
            'profesiKabupaten'
        ));
    }

    public function ketersediaanFasilitas() 
    {
        return view('Kesehatan.ketersediaan-fasilitas');
    }
}