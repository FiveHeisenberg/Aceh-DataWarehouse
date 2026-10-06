<?php

namespace App\Http\Controllers\Dispenda;

use App\Exports\ObjekPajakExport;
use App\Exports\TagihanExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DispendaController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

        $db = DB::connection('mysql');

        // dim_waktu memuat tahun tanpa data tagihan (mis. 2027), jadi ambil dari factsheet
        $availableYears = $db->table('dwh.fact_tagihan')
            ->select('tahun_pajak')
            ->whereNotNull('tahun_pajak')
            ->distinct()
            ->orderBy('tahun_pajak', 'desc')
            ->pluck('tahun_pajak')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        if (! in_array((int) $tahun, array_map('intval', $availableYears))) {
            $tahun = $availableYears[0];
        }

        // dim_wilayah satu baris per desa, collapse dulu supaya tidak ada duplikat opsi
        $wilayahOptions = $db->table('dwh.dim_wilayah')
            ->select('id_kabupaten_kota', 'nama_kabupaten_kota')
            ->distinct()
            ->orderBy('nama_kabupaten_kota')
            ->get()
            ->map(fn ($w) => ['id' => $w->id_kabupaten_kota, 'name' => $w->nama_kabupaten_kota])
            ->values();

        $wilayahId = (string) $request->query('wilayah_id', 'all');

        if (! $wilayahOptions->contains('id', $wilayahId)) {
            $wilayahId = 'all';
        }

        // seluruh akumulasi fact_tagihan, tanpa filter tahun
        $totalPendapatan = $db->table('dwh.fact_tagihan')
            ->where('status_tagihan', 'Lunas')
            ->sum('nominal_tagihan');

        $totalTunggakan = $db->table('dwh.fact_tagihan')
            ->where('status_tagihan', 'Belum Lunas')
            ->sum('nominal_tagihan');

        $jumlahLunas = $db->table('dwh.fact_tagihan')
            ->where('status_tagihan', 'Lunas')
            ->count();

        $jumlahBelumLunas = $db->table('dwh.fact_tagihan')
            ->where('status_tagihan', 'Belum Lunas')
            ->count();

        $jumlahTagihan = $jumlahLunas + $jumlahBelumLunas;
        $rasioKepatuhan = $jumlahTagihan > 0 ? ($jumlahLunas / $jumlahTagihan) * 100 : 0;

        $revenueByRegency = array_fill_keys([
            'Aceh Barat', 'Aceh Barat Daya', 'Aceh Besar', 'Aceh Jaya', 'Aceh Selatan',
            'Aceh Singkil', 'Aceh Tamiang', 'Aceh Tengah', 'Aceh Tenggara', 'Aceh Timur',
            'Aceh Utara', 'Bener Meriah', 'Bireuen', 'Gayo Lues', 'Nagan Raya', 'Pidie',
            'Pidie Jaya', 'Simeulue', 'Banda Aceh', 'Langsa', 'Lhokseumawe', 'Sabang',
            'Subulussalam',
        ], 0);

        $revenueByRegencyRows = $db->table('dwh.fact_tagihan')
            ->join('dwh.dim_wajib_pajak', 'dwh.fact_tagihan.wajib_pajak_key', '=', 'dwh.dim_wajib_pajak.wajib_pajak_key')
            // dim_wilayah stores one row per desa, without distinct() the SUM fans out
            ->joinSub(
                $db->table('dwh.dim_wilayah')->select('id_kabupaten_kota', 'nama_kabupaten_kota')->distinct(),
                'wilayah',
                'dwh.dim_wajib_pajak.id_kabupaten_kota',
                '=',
                'wilayah.id_kabupaten_kota'
            )
            ->where('dwh.fact_tagihan.status_tagihan', 'Lunas')
            ->select(
                'wilayah.nama_kabupaten_kota',
                DB::raw('SUM(dwh.fact_tagihan.nominal_tagihan) as total')
            )
            ->groupBy('wilayah.nama_kabupaten_kota')
            ->get();

        $totalRupiah = array_fill_keys(array_keys($revenueByRegency), 0);

        foreach ($revenueByRegencyRows as $row) {
            $name = str_replace(['Kabupaten ', 'Kota '], '', (string) $row->nama_kabupaten_kota);
            if (array_key_exists($name, $revenueByRegency)) {
                // peta pakai juta (bulat), tabel butuh rupiah penuh
                $revenueByRegency[$name] += (int) ($row->total / 1_000_000);
                $totalRupiah[$name] += (int) $row->total;
            }
        }

        $revenueByRegency = array_map(
            fn (string $name, int $value, int $total): array => [
                'name' => $name,
                'value' => $value,
                'total' => $total,
            ],
            array_keys($revenueByRegency),
            array_values($revenueByRegency),
            array_values($totalRupiah)
        );

        $bulanPendapatan = $db->table('dwh.fact_tagihan')
            ->selectRaw('MONTH(dwh.fact_tagihan.tanggal_jatuh_tempo) as bulan, SUM(dwh.fact_tagihan.nominal_tagihan) as total')
            ->where('dwh.fact_tagihan.status_tagihan', 'Lunas')
            ->where('dwh.fact_tagihan.tahun_pajak', $tahun)
            // fact_tagihan tidak punya id_kabupaten_kota, ambil lewat wajib_pajak
            ->when(
                $wilayahId !== 'all',
                fn ($q) => $q->join('dwh.dim_wajib_pajak', 'dwh.fact_tagihan.wajib_pajak_key', '=', 'dwh.dim_wajib_pajak.wajib_pajak_key')
                    ->where('dwh.dim_wajib_pajak.id_kabupaten_kota', $wilayahId)
            )
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        // fact_tagihan hanya punya tanggal_jatuh_tempo, jadi bulan = bulan jatuh tempo
        $taxTrend = [];
        foreach (['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'] as $i => $nama) {
            $taxTrend[] = [
                'month' => $nama,
                'pajak' => (int) ($bulanPendapatan[$i + 1] ?? 0),
            ];
        }

        return view('Dispenda.dashboard', [
            'tahun' => (int) $tahun,
            'availableYears' => array_map('intval', $availableYears),
            'totalPendapatan' => (int) $totalPendapatan,
            'targetRealisasi' => 14.5,
            'totalTunggakan' => (int) $totalTunggakan,
            'rasioKepatuhan' => round($rasioKepatuhan, 1),
            'revenueByRegency' => $revenueByRegency,
            'taxTrend' => $taxTrend,
            'wilayahId' => $wilayahId,
            'wilayahOptions' => $wilayahOptions,
        ]);
    }

    public function dataTagihan(Request $request)
    {
        $db = DB::connection('mysql');

        $availableYears = $db->table('dwh.fact_tagihan')
            ->select('tahun_pajak')
            ->whereNotNull('tahun_pajak')
            ->distinct()
            ->orderBy('tahun_pajak', 'desc')
            ->pluck('tahun_pajak')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        $wilayahOptions = $db->table('dwh.dim_wilayah')
            ->select('id_kabupaten_kota', 'nama_kabupaten_kota')
            ->distinct()
            ->orderBy('nama_kabupaten_kota')
            ->get()
            ->map(fn ($w) => ['id' => $w->id_kabupaten_kota, 'name' => $w->nama_kabupaten_kota])
            ->values();

        $kategoriPajakOptions = $db->table('dwh.dim_kategori_pajak')
            ->select('kategori_pajak_key', 'nama_pajak')
            ->whereNotNull('nama_pajak')
            ->orderBy('nama_pajak')
            ->get()
            ->map(fn ($k) => ['id' => $k->kategori_pajak_key, 'name' => $k->nama_pajak])
            ->values();

        $search = $request->input('search', '');
        $status = $request->input('status', 'Semua');
        $tahun = $request->input('tahun', date('Y'));
        $kabupaten = $request->input('kabupaten', 'Semua');
        $kategoriPajak = $request->input('kategori_pajak', 'Semua');

        $baseQuery = $db->table('dwh.fact_tagihan')
            ->join('dwh.dim_wajib_pajak', 'dwh.fact_tagihan.wajib_pajak_key', '=', 'dwh.dim_wajib_pajak.wajib_pajak_key')
            ->joinSub(
                $db->table('dwh.dim_wilayah')->select('id_kabupaten_kota', 'nama_kabupaten_kota')->distinct(),
                'wilayah',
                'dwh.dim_wajib_pajak.id_kabupaten_kota',
                '=',
                'wilayah.id_kabupaten_kota'
            );

        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('dwh.fact_tagihan.id_tagihan', 'like', "%{$search}%")
                    ->orWhere('dwh.dim_wajib_pajak.nama_lengkap', 'like', "%{$search}%");
            });
        }

        if ($status && $status !== 'Semua') {
            $baseQuery->where('dwh.fact_tagihan.status_tagihan', $status);
        }

        if ($tahun) {
            $baseQuery->where('dwh.fact_tagihan.tahun_pajak', $tahun);
        }

        if ($kabupaten && $kabupaten !== 'Semua') {
            $baseQuery->where('dwh.dim_wajib_pajak.id_kabupaten_kota', $kabupaten);
        }

        if ($kategoriPajak && $kategoriPajak !== 'Semua') {
            $baseQuery->where('dwh.fact_tagihan.kategori_pajak_key', $kategoriPajak);
        }

        $totalTagihan = (clone $baseQuery)->count();
        $totalLunas = (clone $baseQuery)->where('dwh.fact_tagihan.status_tagihan', 'Lunas')->sum('dwh.fact_tagihan.nominal_tagihan');
        $totalBelumLunas = (clone $baseQuery)->where('dwh.fact_tagihan.status_tagihan', 'Belum Lunas')->sum('dwh.fact_tagihan.nominal_tagihan');

        $query = (clone $baseQuery)->select(
            'dwh.fact_tagihan.id_tagihan',
            'dwh.dim_wajib_pajak.nama_lengkap as nama_wp',
            'wilayah.nama_kabupaten_kota',
            'dwh.fact_tagihan.nominal_tagihan',
            'dwh.fact_tagihan.tanggal_jatuh_tempo',
            'dwh.fact_tagihan.status_tagihan'
        );

        $tagihan = $query->orderBy('dwh.fact_tagihan.tanggal_jatuh_tempo', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('Dispenda.data_tagihan', [
            'tagihan' => $tagihan,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'tahun' => (int) $tahun,
                'kabupaten' => $kabupaten,
                'kategori_pajak' => $kategoriPajak,
            ],
            'availableYears' => array_map('intval', $availableYears),
            'wilayahOptions' => $wilayahOptions,
            'kategoriPajakOptions' => $kategoriPajakOptions,
            'statistics' => [
                'totalTagihan' => $totalTagihan,
                'totalLunas' => (int) $totalLunas,
                'totalBelumLunas' => (int) $totalBelumLunas,
            ],
        ]);
    }

    public function detailTagihan($id)
    {
        $db = DB::connection('mysql');

        $detail = $db->table('dwh.fact_tagihan')
            ->join('dwh.dim_wajib_pajak', 'dwh.fact_tagihan.wajib_pajak_key', '=', 'dwh.dim_wajib_pajak.wajib_pajak_key')
            ->leftJoin('dwh.dim_objek_pajak', 'dwh.fact_tagihan.objek_pajak_key', '=', 'dwh.dim_objek_pajak.objek_pajak_key')
            ->leftJoin('dwh.dim_kategori_pajak', 'dwh.fact_tagihan.kategori_pajak_key', '=', 'dwh.dim_kategori_pajak.kategori_pajak_key')
            ->leftJoinSub(
                $db->table('dwh.dim_wilayah')->select('id_kabupaten_kota', 'nama_kabupaten_kota')->distinct(),
                'wilayah',
                'dwh.dim_wajib_pajak.id_kabupaten_kota',
                '=',
                'wilayah.id_kabupaten_kota'
            )
            ->where('dwh.fact_tagihan.id_tagihan', $id)
            ->select(
                'dwh.fact_tagihan.id_tagihan',
                'dwh.fact_tagihan.tahun_pajak',
                'dwh.fact_tagihan.nominal_tagihan',
                'dwh.fact_tagihan.tanggal_jatuh_tempo',
                'dwh.fact_tagihan.status_tagihan',
                'dwh.dim_wajib_pajak.nik_wp',
                'dwh.dim_wajib_pajak.nama_lengkap as nama_wp',
                'dwh.dim_wajib_pajak.alamat',
                'dwh.dim_wajib_pajak.npwpd',
                'wilayah.nama_kabupaten_kota',
                'dwh.dim_kategori_pajak.nama_pajak',
                'dwh.dim_kategori_pajak.tarif_persentase',
                'dwh.dim_objek_pajak.nomor_identitas_aset',
                'dwh.dim_objek_pajak.rincian_objek',
                'dwh.dim_objek_pajak.nilai_aset'
            )
            ->first();

        if (! $detail) {
            abort(404, 'Tagihan tidak ditemukan');
        }

        return response()->json($detail);
    }

    public function exportTagihan(Request $request)
    {
        $search = $request->input('search', '');
        $status = $request->input('status', 'Semua');
        $tahun = $request->input('tahun', date('Y'));
        $kabupaten = $request->input('kabupaten', 'Semua');
        $kategoriPajak = $request->input('kategori_pajak', 'Semua');

        $filename = 'Data_Tagihan_'.$tahun.'_'.date('Ymd_His').'.xlsx';

        return Excel::download(new TagihanExport($search, $status, $tahun, $kabupaten, $kategoriPajak), $filename);
    }

    public function objekPajak(Request $request)
    {
        $db = DB::connection('mysql');

        $kategoriOptions = $db->table('dwh.dim_kategori_pajak')
            ->select('kategori_pajak_key', 'nama_pajak')
            ->whereNotNull('nama_pajak')
            ->orderBy('nama_pajak')
            ->get()
            ->map(fn ($k) => ['id' => $k->kategori_pajak_key, 'name' => $k->nama_pajak])
            ->values();

        $wilayahOptions = $db->table('dwh.dim_wilayah')
            ->select('id_kabupaten_kota', 'nama_kabupaten_kota')
            ->distinct()
            ->orderBy('nama_kabupaten_kota')
            ->get()
            ->map(fn ($w) => ['id' => $w->id_kabupaten_kota, 'name' => $w->nama_kabupaten_kota])
            ->values();

        $search = $request->input('search', '');
        $kategori = $request->input('kategori', 'Semua');
        $kabupaten = $request->input('kabupaten', 'Semua');
        $status = $request->input('status', 'Semua');

        $baseQuery = $db->table('dwh.dim_objek_pajak')
            ->join('dwh.dim_wajib_pajak', 'dwh.dim_objek_pajak.nik_wp', '=', 'dwh.dim_wajib_pajak.nik_wp')
            ->join('dwh.dim_kategori_pajak', 'dwh.dim_objek_pajak.id_kategori', '=', 'dwh.dim_kategori_pajak.id_kategori')
            ->leftJoinSub(
                $db->table('dwh.dim_wilayah')->select('id_kabupaten_kota', 'nama_kabupaten_kota')->distinct(),
                'wilayah',
                'dwh.dim_wajib_pajak.id_kabupaten_kota',
                '=',
                'wilayah.id_kabupaten_kota'
            );

        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('dwh.dim_objek_pajak.id_objek', 'like', "%{$search}%")
                    ->orWhere('dwh.dim_wajib_pajak.nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('dwh.dim_objek_pajak.nomor_identitas_aset', 'like', "%{$search}%");
            });
        }

        if ($kategori && $kategori !== 'Semua') {
            $baseQuery->where('dwh.dim_objek_pajak.id_kategori', $kategori);
        }

        if ($kabupaten && $kabupaten !== 'Semua') {
            $baseQuery->where('dwh.dim_wajib_pajak.id_kabupaten_kota', $kabupaten);
        }

        $totalObjek = (clone $baseQuery)->count();
        $totalPotensi = (clone $baseQuery)->sum('dwh.dim_objek_pajak.nilai_aset');

        $thisYear = (int) date('Y');
        $lastYear = $thisYear - 1;

        $countThisYear = (clone $baseQuery)->whereYear('dwh.dim_objek_pajak.waktu_load', $thisYear)->count();
        $countLastYear = (clone $baseQuery)->whereYear('dwh.dim_objek_pajak.waktu_load', $lastYear)->count();

        $pertumbuhan = $countLastYear > 0 ? (($countThisYear - $countLastYear) / $countLastYear) * 100 : 0;

        // Chart WAJIB clone $baseQuery, bukan query kosong, supaya ikut filter aktif.
        // PDO mengembalikan SUM/COUNT sebagai string ("44730000000") dan Laravel tidak
        // mem-cast hasil query builder ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â Recharts gagal menghitung slice kalau dapat string.
        $trendPendaftaran = (clone $baseQuery)
            ->selectRaw('YEAR(dwh.dim_objek_pajak.waktu_load) as tahun, COUNT(*) as total')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($row) => ['tahun' => (int) $row->tahun, 'total' => (int) $row->total])
            ->reverse()
            ->values();

        $komposisiPotensi = (clone $baseQuery)
            ->select('dwh.dim_kategori_pajak.nama_pajak', DB::raw('SUM(dwh.dim_objek_pajak.nilai_aset) as total'))
            ->groupBy('dwh.dim_kategori_pajak.nama_pajak')
            ->orderBy('nama_pajak')
            ->get()
            ->map(fn ($row) => ['nama_pajak' => $row->nama_pajak, 'total' => (int) $row->total])
            ->values();

        $objekPajak = (clone $baseQuery)
            ->select(
                'dwh.dim_objek_pajak.id_objek',
                'dwh.dim_kategori_pajak.nama_pajak',
                'dwh.dim_wajib_pajak.nama_lengkap as pemilik',
                'dwh.dim_wajib_pajak.alamat',
                'wilayah.nama_kabupaten_kota',
                'dwh.dim_objek_pajak.nilai_aset',
                'dwh.dim_objek_pajak.nomor_identitas_aset'
            )
            ->orderBy('dwh.dim_objek_pajak.waktu_load', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('Dispenda.objek_pajak', [
            'objekPajak' => $objekPajak,
            'filters' => [
                'search' => $search,
                'kategori' => $kategori,
                'kabupaten' => $kabupaten,
                'status' => $status,
            ],
            'kategoriOptions' => $kategoriOptions,
            'wilayahOptions' => $wilayahOptions,
            'statistics' => [
                'totalObjek' => $totalObjek,
                'totalPotensi' => (int) $totalPotensi,
                'pertumbuhan' => round($pertumbuhan, 1),
                'objekTahunIni' => $countThisYear,
                'tahunAcuan' => $thisYear,
            ],
            'chartData' => [
                'trendPendaftaran' => $trendPendaftaran,
                'komposisiPotensi' => $komposisiPotensi,
            ],
        ]);
    }

    public function exportObjekPajak(Request $request)
    {
        $search = $request->input('search', '');
        $kategori = $request->input('kategori', 'Semua');
        $kabupaten = $request->input('kabupaten', 'Semua');
        $status = $request->input('status', 'Semua');

        $filename = 'Master_Objek_Pajak_'.date('Ymd_His').'.xlsx';

        return Excel::download(new ObjekPajakExport($search, $kategori, $kabupaten, $status), $filename);
    }

    public function detailObjekPajak($id)
    {
        $db = DB::connection('mysql');

        $detail = $db->table('dwh.dim_objek_pajak')
            ->join('dwh.dim_wajib_pajak', 'dwh.dim_objek_pajak.nik_wp', '=', 'dwh.dim_wajib_pajak.nik_wp')
            ->join('dwh.dim_kategori_pajak', 'dwh.dim_objek_pajak.id_kategori', '=', 'dwh.dim_kategori_pajak.id_kategori')
            ->leftJoinSub(
                $db->table('dwh.dim_wilayah')->select('id_kabupaten_kota', 'nama_kabupaten_kota')->distinct(),
                'wilayah',
                'dwh.dim_wajib_pajak.id_kabupaten_kota',
                '=',
                'wilayah.id_kabupaten_kota'
            )
            ->where('dwh.dim_objek_pajak.id_objek', $id)
            ->select(
                'dwh.dim_objek_pajak.id_objek',
                'dwh.dim_objek_pajak.nomor_identitas_aset',
                'dwh.dim_objek_pajak.rincian_objek',
                'dwh.dim_objek_pajak.nilai_aset',
                'dwh.dim_wajib_pajak.nik_wp',
                'dwh.dim_wajib_pajak.nama_lengkap as pemilik',
                'dwh.dim_wajib_pajak.alamat',
                'dwh.dim_wajib_pajak.npwpd',
                'wilayah.nama_kabupaten_kota',
                'dwh.dim_kategori_pajak.nama_pajak',
                'dwh.dim_kategori_pajak.tarif_persentase'
            )
            ->first();

        if (! $detail) {
            abort(404, 'Objek pajak tidak ditemukan');
        }

        return response()->json($detail);
    }
}
