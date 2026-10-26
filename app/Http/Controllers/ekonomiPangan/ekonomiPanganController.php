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

    public function peternakan(Request $request)
    {
        $tahunList = DB::table('fact_pangan_populasi_ternak as f')
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

        $satuanPopulasi = 'ekor';
        $satuanProduksi = 'ton';

        $warnaTernak = [
            'Ayam' => '#f59e0b',
            'Itik' => '#14b8a6',
            'Sapi' => '#8b5cf6',
            'Kambing' => '#3b82f6',
            'Kerbau' => '#ef4444',
            'Domba' => '#06b6d4',
        ];

        $urutanTernak = ['Sapi', 'Ayam', 'Itik', 'Kambing', 'Kerbau', 'Domba'];

        $jenisTernakList = DB::table('dim_pangan_jenis_ternak')
            ->pluck('nama_jenis_ternak')
            ->sortBy(fn ($nama) => array_search($nama, $urutanTernak, true))
            ->values();

        $populasi = DB::table('fact_pangan_populasi_ternak as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->join('dim_pangan_jenis_ternak as j', 'j.jenis_ternak_key', '=', 'f.jenis_ternak_key')
            ->where('w.tahun', $tahunAktif)
            ->select('j.nama_jenis_ternak as label')
            ->selectRaw('SUM(f.jumlah_ekor) as jumlah')
            ->groupBy('j.nama_jenis_ternak')
            ->get()
            ->map(fn ($row) => [
                'label' => $row->label,
                'jumlah' => (int) $row->jumlah,
                'warna' => $warnaTernak[$row->label] ?? '#64748b',
            ])
            ->sortByDesc('jumlah')
            ->values()
            ->all();

        $totalPopulasi = array_sum(array_column($populasi, 'jumlah'));

        $produksiRows = DB::table('fact_pangan_produksi_peternakan as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->join('dim_pangan_jenis_ternak as j', 'j.jenis_ternak_key', '=', 'f.jenis_ternak_key')
            ->join('dim_pangan_jenis_produk_ternak as p', 'p.jenis_produk_key', '=', 'f.jenis_produk_key')
            ->where('w.tahun', $tahunAktif)
            ->select('p.nama_produk', 'p.jenis_produk_key', 'j.nama_jenis_ternak')
            ->selectRaw('SUM(f.jumlah_produksi_ton) as jumlah')
            ->groupBy('p.nama_produk', 'p.jenis_produk_key', 'j.nama_jenis_ternak')
            ->orderBy('p.jenis_produk_key')
            ->orderByDesc('jumlah')
            ->get();

        $produksiPerProduk = $produksiRows
            ->groupBy('nama_produk')
            ->map(fn ($rows) => $rows->map(fn ($row) => [
                $row->nama_jenis_ternak,
                (float) $row->jumlah,
            ])->values()->all())
            ->all();

        $labelKombinasi = [];

        foreach ($produksiPerProduk as $produk => $rows) {
            foreach ($rows as $row) {
                $labelKombinasi[] = $produk.' '.$row[0];
            }
        }

        $kabupatenRows = DB::table('fact_pangan_produksi_peternakan as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->join('dim_pangan_jenis_ternak as j', 'j.jenis_ternak_key', '=', 'f.jenis_ternak_key')
            ->join('dim_pangan_jenis_produk_ternak as p', 'p.jenis_produk_key', '=', 'f.jenis_produk_key')
            ->join('dim_pangan_wilayah as wi', 'wi.wilayah_key', '=', 'f.wilayah_key')
            ->where('w.tahun', $tahunAktif)
            ->select('wi.nama_kabupaten_kota as wilayah', 'p.nama_produk', 'j.nama_jenis_ternak')
            ->selectRaw('SUM(f.jumlah_produksi_ton) as jumlah')
            ->groupBy('wi.nama_kabupaten_kota', 'p.nama_produk', 'j.nama_jenis_ternak')
            ->get();

        $kabupatenList = DB::table('dim_pangan_wilayah')
            ->orderBy('id_kabupaten_kota')
            ->pluck('nama_kabupaten_kota')
            ->values()
            ->all();

        $nilaiKabupaten = [];

        foreach ($kabupatenList as $kabupaten) {
            $nilaiKabupaten[$kabupaten] = array_fill(0, count($labelKombinasi), 0);
        }

        $indeksLabel = array_flip($labelKombinasi);

        foreach ($kabupatenRows as $row) {
            $label = $row->nama_produk.' '.$row->nama_jenis_ternak;

            if (! isset($indeksLabel[$label])) {
                continue;
            }

            $nilaiKabupaten[$row->wilayah][$indeksLabel[$label]] = (float) $row->jumlah;
        }

        $petaPopulasi = DB::table('fact_pangan_populasi_ternak as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->join('dim_pangan_jenis_ternak as j', 'j.jenis_ternak_key', '=', 'f.jenis_ternak_key')
            ->join('dim_pangan_wilayah as wi', 'wi.wilayah_key', '=', 'f.wilayah_key')
            ->where('w.tahun', $tahunAktif)
            ->select('wi.nama_kabupaten_kota as wilayah', 'j.nama_jenis_ternak as jenis')
            ->selectRaw('SUM(f.jumlah_ekor) as jumlah')
            ->groupBy('wi.nama_kabupaten_kota', 'j.nama_jenis_ternak')
            ->get()
            ->map(fn ($row) => [
                'wilayah' => $row->wilayah,
                'jenis' => $row->jenis,
                'jumlah' => (int) $row->jumlah,
            ])
            ->values()
            ->all();

        return view('Ekonomi-Pangan.Peternakan', compact(
            'tahunList',
            'tahunAktif',
            'satuanPopulasi',
            'satuanProduksi',
            'jenisTernakList',
            'populasi',
            'totalPopulasi',
            'produksiPerProduk',
            'labelKombinasi',
            'nilaiKabupaten',
            'kabupatenList',
            'petaPopulasi'
        ));
    }

    public function pangan(Request $request)
    {
        $tahunHarga = DB::table('fact_pangan_harga as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->distinct()
            ->pluck('w.tahun')
            ->map(fn ($tahun) => (int) $tahun);

        $tahunStok = DB::table('fact_pangan_ketersediaan as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->distinct()
            ->pluck('w.tahun')
            ->map(fn ($tahun) => (int) $tahun);

        $tahunList = $tahunHarga
            ->merge($tahunStok)
            ->unique()
            ->sortDesc()
            ->values();

        $tahunAktif = (int) $request->query('tahun', $tahunList->first());

        if (! $tahunList->contains($tahunAktif)) {
            $tahunAktif = (int) ($tahunList->first() ?? 0);
        }

        $komoditasHarga = DB::table('fact_pangan_harga as f')
            ->join('dim_pangan_komoditas as k', 'k.komoditas_key', '=', 'f.komoditas_key')
            ->distinct()
            ->orderBy('k.nama_komoditas')
            ->pluck('k.nama_komoditas')
            ->values()
            ->all();

        $komoditasStok = DB::table('fact_pangan_ketersediaan as f')
            ->join('dim_pangan_komoditas as k', 'k.komoditas_key', '=', 'f.komoditas_key')
            ->distinct()
            ->orderBy('k.nama_komoditas')
            ->pluck('k.nama_komoditas')
            ->values()
            ->all();

        $wilayahList = DB::table('dim_pangan_wilayah')
            ->orderBy('id_kabupaten_kota')
            ->pluck('nama_kabupaten_kota')
            ->values()
            ->all();

        $kabHargaList = $wilayahList;
        $wilayahStokList = $wilayahList;

        $satuanHarga = DB::table('fact_pangan_harga as f')
            ->join('dim_pangan_komoditas as k', 'k.komoditas_key', '=', 'f.komoditas_key')
            ->select('k.nama_komoditas', 'f.satuan')
            ->distinct()
            ->get()
            ->mapWithKeys(fn ($row) => [$row->nama_komoditas => $row->satuan])
            ->all();

        $hargaBulanan = DB::table('fact_pangan_harga as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->join('dim_pangan_komoditas as k', 'k.komoditas_key', '=', 'f.komoditas_key')
            ->join('dim_pangan_wilayah as wi', 'wi.wilayah_key', '=', 'f.wilayah_key')
            ->where('w.tahun', $tahunAktif)
            ->select('k.nama_komoditas as komoditas', 'wi.nama_kabupaten_kota as wilayah', 'w.bulan', 'w.nama_bulan')
            ->selectRaw('AVG(f.harga) as harga')
            ->groupBy('k.nama_komoditas', 'wi.nama_kabupaten_kota', 'w.bulan', 'w.nama_bulan')
            ->orderBy('w.bulan')
            ->get();

        $bulanHargaMap = $hargaBulanan->pluck('nama_bulan', 'bulan')->sortKeys();
        $bulanHarga = $bulanHargaMap->values()->all();
        $bulanHargaIndex = array_flip($bulanHargaMap->keys()->all());

        $trenHarga = [];

        foreach ($komoditasHarga as $komoditas) {
            foreach ($kabHargaList as $kabupaten) {
                $trenHarga[$komoditas][$kabupaten] = array_fill(0, count($bulanHarga), 0);
            }
        }

        foreach ($hargaBulanan as $row) {
            $index = $bulanHargaIndex[$row->bulan] ?? null;

            if ($index === null || ! isset($trenHarga[$row->komoditas][$row->wilayah])) {
                continue;
            }

            $trenHarga[$row->komoditas][$row->wilayah][$index] = round((float) $row->harga, 2);
        }

        $tanggalHarga = DB::table('fact_pangan_harga as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->where('w.tahun', $tahunAktif)
            ->max('w.tanggal');

        $perbandingan = [];

        if ($tanggalHarga) {
            $hargaTerbaru = DB::table('fact_pangan_harga as f')
                ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
                ->join('dim_pangan_komoditas as k', 'k.komoditas_key', '=', 'f.komoditas_key')
                ->join('dim_pangan_wilayah as wi', 'wi.wilayah_key', '=', 'f.wilayah_key')
                ->where('w.tahun', $tahunAktif)
                ->where('w.tanggal', $tanggalHarga)
                ->select('k.nama_komoditas as komoditas', 'wi.nama_kabupaten_kota as wilayah', 'f.harga')
                ->get();

            foreach ($komoditasHarga as $komoditas) {
                foreach ($kabHargaList as $kabupaten) {
                    $perbandingan[$komoditas][$kabupaten] = 0;
                }
            }

            foreach ($hargaTerbaru as $row) {
                if (! isset($perbandingan[$row->komoditas][$row->wilayah])) {
                    continue;
                }

                $perbandingan[$row->komoditas][$row->wilayah] = round((float) $row->harga, 2);
            }
        }

        $stokRows = DB::table('fact_pangan_ketersediaan as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->join('dim_pangan_komoditas as k', 'k.komoditas_key', '=', 'f.komoditas_key')
            ->join('dim_pangan_wilayah as wi', 'wi.wilayah_key', '=', 'f.wilayah_key')
            ->where('w.tahun', $tahunAktif)
            ->select('k.nama_komoditas as komoditas', 'wi.nama_kabupaten_kota as wilayah', 'w.bulan', 'w.nama_bulan')
            ->selectRaw('SUM(f.stok_ton) as stok')
            ->groupBy('k.nama_komoditas', 'wi.nama_kabupaten_kota', 'w.bulan', 'w.nama_bulan')
            ->orderBy('w.bulan')
            ->get();

        $bulanStokMap = $stokRows->pluck('nama_bulan', 'bulan')->sortKeys();
        $bulanStok = $bulanStokMap->values()->all();
        $bulanStokIndex = array_flip($bulanStokMap->keys()->all());

        $stok = [];

        foreach ($komoditasStok as $komoditas) {
            foreach ($wilayahStokList as $wilayah) {
                $stok[$komoditas][$wilayah] = array_fill(0, count($bulanStok), 0);
            }
        }

        foreach ($stokRows as $row) {
            $index = $bulanStokIndex[$row->bulan] ?? null;

            if ($index === null || ! isset($stok[$row->komoditas][$row->wilayah])) {
                continue;
            }

            $stok[$row->komoditas][$row->wilayah][$index] = round((float) $row->stok, 2);
        }

        return view('Ekonomi-Pangan.Pangan', compact(
            'tahunList',
            'tahunAktif',
            'komoditasHarga',
            'komoditasStok',
            'kabHargaList',
            'wilayahStokList',
            'satuanHarga',
            'bulanHarga',
            'trenHarga',
            'tanggalHarga',
            'perbandingan',
            'bulanStok',
            'stok'
        ));
    }

    public function perikanan(Request $request)
    {
        $tahunList = DB::table('fact_pangan_produksi_perikanan as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->select('w.tahun')
            ->distinct()
            ->orderByDesc('w.tahun')
            ->pluck('w.tahun')
            ->map(fn ($tahun) => (int) $tahun)
            ->values();

        $tahunAktif = (int) $request->query('tahun', $tahunList->first());

        if (! $tahunList->contains($tahunAktif)) {
            $tahunAktif = (int) ($tahunList->first() ?? 0);
        }

        $jenisPerikananList = [
            'tangkap'  => 'Perikanan Tangkap',
            'budidaya' => 'Perikanan Budidaya',
        ];
        $jenisAwal = 'tangkap';

        // Total produksi per kategori per tahun
        $perTahun = ['tangkap' => [], 'budidaya' => []];

        $totalTahunan = DB::table('fact_pangan_produksi_perikanan as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->join('dim_pangan_jenis_perikanan as jp', 'jp.jenis_perikanan_key', '=', 'f.jenis_perikanan_key')
            ->selectRaw('w.tahun, jp.kategori, SUM(f.volume_ton) as total')
            ->groupBy('w.tahun', 'jp.kategori')
            ->orderBy('w.tahun')
            ->get();

        foreach ($totalTahunan as $row) {
            $key = strtolower($row->kategori);

            if (! isset($perTahun[$key])) {
                continue;
            }

            $perTahun[$key][(int) $row->tahun] = round((float) $row->total, 2);
        }

        foreach ($perTahun as $key => $rows) {
            ksort($perTahun[$key]);
        }

        // Produksi per jenis ikan per kategori per tahun
        $perJenis = ['tangkap' => [], 'budidaya' => []];

        $jenisRows = DB::table('fact_pangan_produksi_perikanan as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->join('dim_pangan_jenis_perikanan as jp', 'jp.jenis_perikanan_key', '=', 'f.jenis_perikanan_key')
            ->join('dim_pangan_jenis_ikan as ji', 'ji.jenis_ikan_key', '=', 'f.jenis_ikan_key')
            ->selectRaw('w.tahun, jp.kategori, ji.nama_ikan, SUM(f.volume_ton) as total')
            ->groupBy('w.tahun', 'jp.kategori', 'ji.nama_ikan')
            ->get();

        foreach ($jenisRows as $row) {
            $key = strtolower($row->kategori);

            if (! isset($perJenis[$key])) {
                continue;
            }

            $perJenis[$key][(int) $row->tahun][] = [
                'nama'   => $row->nama_ikan,
                'volume' => round((float) $row->total, 2),
            ];
        }

        foreach ($perJenis as $key => $tahunRows) {
            ksort($perJenis[$key]);

            foreach ($perJenis[$key] as $tahun => $rows) {
                usort($rows, fn ($a, $b) => $b['volume'] <=> $a['volume']);
                $perJenis[$key][$tahun] = $rows;
            }
        }

        // Produksi per kabupaten/kota per kategori per tahun
        $perWilayah = ['tangkap' => [], 'budidaya' => []];

        $wilayahRows = DB::table('fact_pangan_produksi_perikanan as f')
            ->join('dim_waktu as w', 'w.waktu_key', '=', 'f.waktu_key')
            ->join('dim_pangan_jenis_perikanan as jp', 'jp.jenis_perikanan_key', '=', 'f.jenis_perikanan_key')
            ->join('dim_pangan_wilayah as wi', 'wi.wilayah_key', '=', 'f.wilayah_key')
            ->selectRaw('w.tahun, jp.kategori, wi.nama_kabupaten_kota as wilayah, SUM(f.volume_ton) as total')
            ->groupBy('w.tahun', 'jp.kategori', 'wi.nama_kabupaten_kota')
            ->get();

        foreach ($wilayahRows as $row) {
            $key = strtolower($row->kategori);

            if (! isset($perWilayah[$key])) {
                continue;
            }

            $perWilayah[$key][(int) $row->tahun][] = [
                'wilayah' => $row->wilayah,
                'volume'  => round((float) $row->total, 2),
            ];
        }

        foreach ($perWilayah as $key => $tahunRows) {
            ksort($perWilayah[$key]);

            foreach ($perWilayah[$key] as $tahun => $rows) {
                usort($rows, fn ($a, $b) => $b['volume'] <=> $a['volume']);
                $perWilayah[$key][$tahun] = $rows;
            }
        }

        return view('Ekonomi-Pangan.Perikanan', compact(
            'tahunList',
            'tahunAktif',
            'jenisPerikananList',
            'jenisAwal',
            'perTahun',
            'perJenis',
            'perWilayah'
        ));
    }
}