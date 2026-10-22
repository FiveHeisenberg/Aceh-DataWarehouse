<?php

namespace App\Http\Controllers\Kesehatan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class KesehatanController extends Controller
{
    public function penyakit()
    {
        return view('Kesehatan.penyakit');
    }

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

}