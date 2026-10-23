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
            ->distinct()
            ->orderByDesc('dw.tahun')
            ->pluck('dw.tahun')
            ->map(fn ($tahun) => (int) $tahun)
            ->values()
            ->all();
        
        $tahunAktif = (int) ($request->input('tahun') ?: ($tahunList[0] ?? date('Y')));

        if (! in_array($tahunAktif, $tahunList, true)) {
            abort(404);
        }

        $base = $db->table('dwh.fact_kesehatan_kasus_penyakit as fk')
            ->join('dwh.dim_waktu as dw', 'fk.waktu_key', '=', 'dw.waktu_key')
            ->where('dw.tahun', $tahunAktif);

        $totalKasus = (int) (clone $base)->sum('fk.jumlah_kasus');

        $penyakit = (clone $base)
            ->join('dwh.dim_penyakit as dp', 'fk.penyakit_key', '=', 'dp.penyakit_key')
            ->select('dp.nama_penyakit as nama', DB::raw('SUM(fk.jumlah_kasus) as jumlah'))
            ->groupBy('dp.nama_penyakit')
            ->orderByDesc('jumlah')
            ->get()
            ->map(fn ($row) => [
                'nama' => $row->nama ?: '-',
                'jumlah' => (int) $row->jumlah,
                'kabupaten' => '-',
            ])
            ->all();

        $kabupaten = (clone $base)
            ->join('dwh.dim_wilayah as wilayah', 'fk.kabupaten_kota_key', '=', 'wilayah.kabupaten_kota_key')
            ->select('wilayah.nama_kabupaten_kota as nama', DB::raw('SUM(fk.jumlah_kasus) as jumlah'))
            ->groupBy('wilayah.nama_kabupaten_kota')
            ->orderByDesc('jumlah')
            ->get()
            ->map(fn ($row) => [
                'nama' => $row->nama ?: '-',
                'jumlah' => (int) $row->jumlah,
            ])
            ->all();

        $ringkasan = array_slice($penyakit, 0, 4);

        $maxPenyakit = max(array_column($penyakit, 'jumlah') ?: [0]);
        $maxKabupaten = max(array_column($kabupaten, 'jumlah') ?: [0]);

        $fmt = fn ($n) => number_format($n, 0, ',', '.');

        return view('Kesehatan.penyakit', compact(
            'tahunList',
            'tahunAktif',
            'totalKasus',
            'penyakit',
            'kabupaten',
            'ringkasan',
            'maxPenyakit',
            'maxKabupaten',
            'fmt'
        ));
    }
}