<?php

namespace App\Http\Controllers\Kesehatan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class KesehatanController extends Controller
{

    public function tenagaMedis()
    {
        return view('Kesehatan.tenaga-medis');
    }

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

        $kelas = $db->table('dwh.dim_kesehatan_rs')
            ->select('id_kelas_rs', 'nama_kelas', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('id_kelas_rs', 'nama_kelas')
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

        $daftarRs = $db->table('dwh.dim_kesehatan_rs')
            ->orderBy('nama_rs')
            ->get([
                'rs_key', 'id_rs', 'nama_rs', 'alamat', 'telepon', 'latitude', 'longitude', 'nama_kelas', 'nama_jenis_rs', 'nama_pemilik', 'nama_kabupaten_kota', 'nama_provinsi',
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
        return $db->table('dwh.dim_kesehatan_rs')
            ->select($kolom, DB::raw('COUNT(*) as jumlah'))
            ->groupBy($kolom)
            ->orderByDesc('jumlah')
            ->orderBy($kolom)
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
            ->select(
                'rs.nama_kabupaten_kota as nama',
                DB::raw('SUM(fk.jumlah_kasus) as jumlah')
            )
            ->whereNotNull('rs.nama_kabupaten_kota')
            ->groupBy('rs.nama_kabupaten_kota')
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
            ->select(
                'rs.nama_kabupaten_kota as nama',
                DB::raw('SUM(fk.jumlah_kasus) as jumlah')
            )
            ->groupBy('rs.nama_kabupaten_kota')
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
            ->join(
                'dwh.dim_kesehatan_penyakit as dp',
                'fk.penyakit_key',
                '=',
                'dp.penyakit_key'
            )
            ->whereNotNull('rs.nama_kabupaten_kota')
            ->select(
                'rs.nama_kabupaten_kota as kabupaten', 'dp.nama_penyakit',
                DB::raw('SUM(fk.jumlah_kasus) as jumlah_penyakit')
            )
            ->groupBy(
                'rs.nama_kabupaten_kota',
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

    
}