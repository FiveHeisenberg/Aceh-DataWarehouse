<?php

namespace App\Http\Controllers\EkonomiPangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EkonomiPanganController extends Controller
{
    public function pertanian(Request $request)
    {
        $tahunList = DB::table('fact_pangan_produksi_pertanian as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->select('w.tahun as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->map(fn ($tahun) => (int) $tahun)
            ->values();

        $tahunAktif = (int) $request->query('tahun', $tahunList->first());

        if (! $tahunList->contains($tahunAktif)) {
            $tahunAktif = (int) ($tahunList->first() ?? 0);
        }

        $satuanLuas = 'ha';
        $satuanProduksi = 'ton';

        $jenisLahanList = DB::table('dim_pangan_jenis_lahan')
            ->orderBy('nama_jenis_lahan')
            ->pluck('nama_jenis_lahan')
            ->values();

        $warnaLahan = [
            'Sawah' => '#16a34a',
            'Ladang/Tegalan' => '#f59e0b',
            'Kebun' => '#8b5a2b',
        ];

        $komposisi = DB::table('fact_pangan_luas_lahan as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->join('dim_pangan_jenis_lahan as j', 'j.jenis_lahan_key', '=', 'f.jenis_lahan_key')
            ->where('w.tahun', $tahunAktif)
            ->select('j.nama_jenis_lahan as label')
            ->selectRaw('SUM(f.luas_ha) as luas')
            ->groupBy('j.nama_jenis_lahan')
            ->orderBy('j.nama_jenis_lahan')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->label,
                'luas' => (float) $row->luas,
                'warna' => $warnaLahan[$row->label] ?? '#64748b',
            ])
            ->values()
            ->all();

        $totalLuas = array_sum(array_column($komposisi, 'luas'));

        $petaLuasLahan = DB::table('fact_pangan_luas_lahan as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->join('dim_pangan_wilayah as wi', 'wi.wilayah_key', '=', 'f.wilayah_key')
            ->join('dim_pangan_jenis_lahan as j', 'j.jenis_lahan_key', '=', 'f.jenis_lahan_key')
            ->where('w.tahun', $tahunAktif)
            ->select('wi.nama_kabupaten_kota as wilayah', 'j.nama_jenis_lahan as jenis')
            ->selectRaw('SUM(f.luas_ha) as luas')
            ->groupBy('wi.nama_kabupaten_kota', 'j.nama_jenis_lahan')
            ->orderBy('wi.nama_kabupaten_kota')
            ->get()
            ->map(fn ($row) => [
                'wilayah' => $row->wilayah,
                'jenis' => $row->jenis,
                'luas' => (float) $row->luas,
            ])
            ->values();

        $kabupatenList = DB::table('dim_pangan_wilayah')
            ->orderBy('id_kabupaten_kota')
            ->pluck('nama_kabupaten_kota')
            ->values()
            ->all();

        $produksi = DB::table('fact_pangan_produksi_pertanian as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->join('dim_pangan_komoditas as k', 'k.komoditas_key', '=', 'f.komoditas_key')
            ->join('dim_pangan_wilayah as wi', 'wi.wilayah_key', '=', 'f.wilayah_key')
            ->where('w.tahun', $tahunAktif)
            ->select('k.nama_komoditas', 'wi.nama_kabupaten_kota')
            ->selectRaw('SUM(f.luas_tanam_ha) as tanam, SUM(f.luas_panen_ha) as panen')
            ->groupBy('k.nama_komoditas', 'wi.nama_kabupaten_kota')
            ->get();

        $komoditasList = $produksi->pluck('nama_komoditas')
            ->unique()
            ->sort()
            ->values()
            ->all();

        $matrix = [];

        foreach ($komoditasList as $komoditas) {
            foreach ($kabupatenList as $kabupaten) {
                $matrix[$komoditas][$kabupaten] = [0, 0];
            }
        }

        foreach ($produksi as $row) {
            $matrix[$row->nama_komoditas][$row->nama_kabupaten_kota] = [
                (float) $row->tanam,
                (float) $row->panen,
            ];
        }

        $produksiPadi = DB::table('fact_pangan_produksi_pertanian as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->join('dim_pangan_komoditas as k', 'k.komoditas_key', '=', 'f.komoditas_key')
            ->join('dim_pangan_wilayah as wi', 'wi.wilayah_key', '=', 'f.wilayah_key')
            ->where('w.tahun', $tahunAktif)
            ->where('k.nama_komoditas', 'Padi')
            ->select('wi.nama_kabupaten_kota as wilayah')
            ->selectRaw('SUM(f.produksi_ton) as jumlah')
            ->groupBy('wi.nama_kabupaten_kota')
            ->orderByDesc('jumlah')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'wilayah' => $row->wilayah,
                'jumlah' => (float) $row->jumlah,
            ])
            ->all();

        $maxProduksi = count($produksiPadi)
            ? max(array_column($produksiPadi, 'jumlah'))
            : 1;

        $petaniBase = DB::table('fact_pangan_petani as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->where('w.tahun', $tahunAktif);

        $totalPetani = (int) (clone $petaniBase)->sum('f.jumlah_petani');

        $petaniTerbanyakRow = (clone $petaniBase)
            ->join('dim_pangan_wilayah as wi', 'wi.wilayah_key', '=', 'f.wilayah_key')
            ->select('wi.nama_kabupaten_kota')
            ->selectRaw('SUM(f.jumlah_petani) as total')
            ->groupBy('wi.nama_kabupaten_kota')
            ->orderByDesc('total')
            ->first();

        $petaniTerbanyak = $petaniTerbanyakRow->nama_kabupaten_kota ?? '-';

        $jumlahKelompok = DB::table('dim_pangan_kelompok_tani')->count();

        return view('Ekonomi-Pangan.Pertanian', compact(
            'tahunList',
            'tahunAktif',
            'satuanLuas',
            'satuanProduksi',
            'jenisLahanList',
            'komposisi',
            'totalLuas',
            'petaLuasLahan',
            'kabupatenList',
            'komoditasList',
            'matrix',
            'produksiPadi',
            'maxProduksi',
            'totalPetani',
            'jumlahKelompok',
            'petaniTerbanyak'
        ));
    }

    public function peternakan()
    {
        return view('Ekonomi-Pangan.Peternakan');
    }

    public function pangan()
    {
        return view('Ekonomi-Pangan.Pangan');
    }

    public function perikanan()
    {
        return view('Ekonomi-Pangan.Perikanan');
    }
}