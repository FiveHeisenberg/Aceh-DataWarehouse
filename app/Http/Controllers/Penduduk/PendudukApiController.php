<?php

namespace App\Http\Controllers\Penduduk;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendudukApiController extends Controller
{
    public function getTahun(): JsonResponse
    {
        try {
            $tahun = DB::table('dim_waktu')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return response()->json([
            'success' => true,
            'data' => $tahun,
        ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Gagal mengambil data tahun: " . $e->getMessage(),
            ], 500);
        }
    }

    public function getJumlahPenduduk(): JsonResponse
    {
        try {
            $data = DB::table('fact_penduduk as fp')
                ->join('dim_waktu as dw', 'fp.waktu_key', '=', 'dw.waktu_key')
                ->select('dw.tahun', DB::raw('COUNT(fp.jumlah_penduduk) as jumlah'))
                ->groupBy('dw.tahun')
                ->orderBy('dw.tahun', 'asc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data penduduk: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getMapData(Request $request): JsonResponse
    {
        try {
            $tahun = $request->input('tahun');

            if (!$tahun) {
                $tahun = DB::table('fact_penduduk as fp')
                    ->join('dim_waktu as wt', 'fp.waktu_key', '=', 'wt.waktu_key')
                    ->max('wt.tahun');
            }

            $current = DB::table('fact_penduduk as fp')
                ->join('dim_waktu as wt', 'fp.waktu_key', '=', 'wt.waktu_key')
                ->join('dim_wilayah as dw', 'fp.wilayah_key', '=', 'dw.wilayah_key')
                ->select(
                    'dw.id_kabupaten_kota as kode',
                    'dw.nama_kabupaten_kota as nama',
                    DB::raw('COUNT(fp.jumlah_penduduk) as jumlah')
                )
                ->where('wt.tahun', $tahun)
                ->groupBy('dw.id_kabupaten_kota', 'dw.nama_kabupaten_kota')
                ->orderBy('jumlah', 'desc')
                ->get();

            $prev = DB::table('fact_penduduk as fp')
                ->join('dim_waktu as wt', 'fp.waktu_key', '=', 'wt.waktu_key')
                ->join('dim_wilayah as dw', 'fp.wilayah_key', '=', 'dw.wilayah_key')
                ->select('dw.id_kabupaten_kota as kode', DB::raw('COUNT(fp.jumlah_penduduk) as jumlah'))
                ->where('wt.tahun', $tahun - 1)
                ->groupBy('dw.id_kabupaten_kota')
                ->pluck('jumlah', 'kode');

            $kabupaten = [];
            $rank = 1;

            foreach ($current as $row) {
                $jumlah = (int) $row->jumlah;
                $prevJumlah = $prev[$row->kode] ?? null;

                $pertumbuhan = ($prevJumlah !== null && $prevJumlah > 0)
                    ? round((($jumlah - $prevJumlah) / $prevJumlah) * 100, 2)
                    : null;

                $kabupaten[] = [
                    'kode' => $row->kode,
                    'nama' => ucwords(strtolower($row->nama)),
                    'jumlah_penduduk' => $jumlah,
                    'pertumbuhan_persen' => $pertumbuhan,
                    'peringkat' => $rank++,
                    'satuan' => 'jiwa',
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Data peta berhasil diambil',
                'data' => [
                    'tahun' => (int) $tahun,
                    'kabupaten' => $kabupaten,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data peta: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getTrendPertumbuhan(Request $request): JsonResponse
    {
        try {
            $wilayah = $request->input('wilayah');

            if (!$wilayah) {
                $kabupaten = DB::table('fact_penduduk as fp')
                    ->join('dim_wilayah as dw', 'fp.wilayah_key', '=', 'dw.wilayah_key')
                    ->select('dw.nama_kabupaten_kota as nama', DB::raw('COUNT(fp.jumlah_penduduk) as jumlah'))
                    ->groupBy('dw.nama_kabupaten_kota')
                    ->orderBy('dw.nama_kabupaten_kota')
                    ->get();

                return response()->json([
                    'success' => true,
                    'message' => 'Daftar kabupaten berhasil diambil',
                    'data' => [
                        'kabupaten' => $kabupaten,
                    ],
                ], 200);
            }

            $tren = DB::table('fact_penduduk as fp')
                ->join('dim_waktu as wt', 'fp.waktu_key', '=', 'wt.waktu_key')
                ->join('dim_wilayah as dw', 'fp.wilayah_key', '=', 'dw.wilayah_key')
                ->select('wt.tahun', DB::raw('COUNT(fp.jumlah_penduduk) as jumlah'))
                ->where('dw.nama_kabupaten_kota', $wilayah)
                ->groupBy('wt.tahun')
                ->orderBy('wt.tahun', 'asc')
                ->get();

            if ($tren->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan untuk wilayah: ' . $wilayah,
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data tren berhasil diambil',
                'data' => [
                    'kabupaten' => ucwords(strtolower($wilayah)),
                    'tren' => $tren->map(fn($r) => [
                        'tahun' => (int) $r->tahun,
                        'jumlah' => (int) $r->jumlah,
                    ])->values(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tren: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getDetailPenduduk(Request $request): JsonResponse
    {
        try {
            $query = DB::table('fact_penduduk as fp')
                ->join('dim_waktu as dw', 'fp.waktu_key', '=', 'dw.waktu_key')
                ->join('dim_wilayah as dww', 'fp.wilayah_key', '=', 'dww.wilayah_key')
                ->select(
                    'dww.nama_kabupaten_kota',
                    'dw.tahun',
                    DB::raw('COUNT(fp.jumlah_penduduk) as jumlah_penduduk')
                )
                ->groupBy('dww.nama_kabupaten_kota', 'dw.tahun');

            $query->when($request->has('tahun') && $request->input('tahun') !== '', function ($q) use ($request) {
                return $q->where('dw.tahun', $request->input('tahun'));
            });

            $query->when($request->filled('search'), function ($q) use ($request) {
                return $q->where('dww.nama_kabupaten_kota', 'like', '%' . $request->input('search') . '%');
            });

            $data = $query->orderByDesc('jumlah_penduduk')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data detail penduduk berhasil diambil',
                'data' => $data->map(fn($r) => [
                    'nama_kabupaten_kota' => $r->nama_kabupaten_kota,
                    'tahun' => (int) $r->tahun,
                    'jumlah_penduduk' => (int) $r->jumlah_penduduk,
                    'satuan' => 'jiwa',
                ])->values(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data detail penduduk: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getKartuKeluargaSummary(Request $request): JsonResponse
    {
        try {
            $tahun = $request->input('tahun');

            if (!$tahun) {
                $tahun = DB::table('fact_penduduk as fp')
                    ->join('dim_waktu as wt', 'fp.waktu_key', '=', 'wt.waktu_key')
                    ->max('wt.tahun');
            }

            $perWilayah = DB::table('fact_penduduk as fp')
                ->join('dim_waktu as wt', 'fp.waktu_key', '=', 'wt.waktu_key')
                ->join('dim_wilayah as dw', 'fp.wilayah_key', '=', 'dw.wilayah_key')
                ->select('dw.nama_kabupaten_kota as nama', DB::raw('COUNT(DISTINCT fp.kartu_keluarga_key) as jumlah'))
                ->whereNotNull('fp.kartu_keluarga_key')
                ->where('wt.tahun', $tahun)
                ->groupBy('dw.nama_kabupaten_kota')
                ->orderByDesc('jumlah')
                ->get();

            $total = (int) $perWilayah->sum('jumlah');

            $prevTotal = DB::table('fact_penduduk as fp')
                ->join('dim_waktu as wt', 'fp.waktu_key', '=', 'wt.waktu_key')
                ->whereNotNull('fp.kartu_keluarga_key')
                ->where('wt.tahun', $tahun - 1)
                ->distinct()
                ->count('fp.kartu_keluarga_key');

            $pertumbuhan = ($prevTotal > 0)
                ? round((($total - $prevTotal) / $prevTotal) * 100, 2)
                : null;

            $terbanyak = $perWilayah->first();

            return response()->json([
                'success' => true,
                'message' => 'Data kartu keluarga berhasil diambil',
                'data' => [
                    'tahun' => (int) $tahun,
                    'total_kk' => $total,
                    'pertumbuhan_persen' => $pertumbuhan,
                    'jumlah_kabupaten' => $perWilayah->count(),
                    'kabupaten_terbanyak' => $terbanyak ? [
                        'nama' => $terbanyak->nama,
                        'jumlah' => (int) $terbanyak->jumlah,
                    ] : null,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kartu keluarga: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getKartuKeluargaTrend(): JsonResponse
    {
        try {
            $data = DB::table('fact_penduduk as fp')
                ->join('dim_waktu as wt', 'fp.waktu_key', '=', 'wt.waktu_key')
                ->select('wt.tahun', DB::raw('COUNT(DISTINCT fp.kartu_keluarga_key) as jumlah'))
                ->whereNotNull('fp.kartu_keluarga_key')
                ->groupBy('wt.tahun')
                ->orderBy('wt.tahun', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data tren kartu keluarga berhasil diambil',
                'data' => $data->map(fn($r) => [
                    'tahun' => (int) $r->tahun,
                    'jumlah' => (int) $r->jumlah,
                ])->values(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tren kartu keluarga: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getKartuKeluargaDetail(Request $request): JsonResponse
    {
        try {
            $tahun = $request->input('tahun');

            if (!$tahun) {
                $tahun = DB::table('fact_penduduk as fp')
                    ->join('dim_waktu as wt', 'fp.waktu_key', '=', 'wt.waktu_key')
                    ->max('wt.tahun');
            }

            $data = DB::table('fact_penduduk as fp')
                ->join('dim_waktu as wt', 'fp.waktu_key', '=', 'wt.waktu_key')
                ->join('dim_wilayah as dw', 'fp.wilayah_key', '=', 'dw.wilayah_key')
                ->select('dw.nama_kabupaten_kota as nama', DB::raw('COUNT(DISTINCT fp.kartu_keluarga_key) as jumlah'))
                ->whereNotNull('fp.kartu_keluarga_key')
                ->where('wt.tahun', $tahun)
                ->groupBy('dw.nama_kabupaten_kota')
                ->orderByDesc('jumlah')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data detail kartu keluarga berhasil diambil',
                'data' => [
                    'tahun' => (int) $tahun,
                    'detail' => $data->map(fn($r) => [
                        'nama_kabupaten_kota' => $r->nama,
                        'jumlah_kk' => (int) $r->jumlah,
                        'satuan' => 'KK',
                    ])->values(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data detail kartu keluarga: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getStrukturUmur(Request $request): JsonResponse
    {
        try {
            $tahun = $request->input('tahun');

            $query = DB::table('fact_penduduk as fp')
                ->join('dim_waktu as dw', 'fp.waktu_key', '=', 'dw.waktu_key')
                ->selectRaw("
                    CASE
                        WHEN fp.umur BETWEEN 0 AND 5 THEN '0-5'
                        WHEN fp.umur BETWEEN 6 AND 9 THEN '6-9'
                        WHEN fp.umur BETWEEN 10 AND 17 THEN '10-17'
                        WHEN fp.umur BETWEEN 18 AND 59 THEN '18-59'
                        WHEN fp.umur >= 60 THEN '60+'
                        ELSE 'Tidak Diketahui'
                    END AS range_umur,
                    CASE
                        WHEN fp.umur BETWEEN 0 AND 5 THEN 'Bayi dan Balita'
                        WHEN fp.umur BETWEEN 6 AND 9 THEN 'Anak-anak'
                        WHEN fp.umur BETWEEN 10 AND 17 THEN 'Remaja'
                        WHEN fp.umur BETWEEN 18 AND 59 THEN 'Dewasa'
                        WHEN fp.umur >= 60 THEN 'Lansia'
                        ELSE 'Tidak Diketahui'
                    END AS kategori,
                    COUNT(*) AS jumlah
                ")
                ->groupByRaw("1, 2");

            if ($tahun) {
                $query->where('dw.tahun', $tahun);
            }

            $data = $query
                ->orderByRaw("FIELD(range_umur, '0-5', '6-9', '10-17', '18-59', '60+', 'Tidak Diketahui')")
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data struktur kelompok umur berhasil diambil',
                'data' => $data->map(fn($r) => [
                    'range_umur' => $r->range_umur,
                    'kategori' => $r->kategori,
                    'jumlah' => (int) $r->jumlah,
                ])->values(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data struktur kelompok umur: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getPiramidaUmur(Request $request): JsonResponse
    {
        try {
            $tahun = $request->input('tahun');

            $query = DB::table('fact_penduduk as fp')
                ->join('dim_waktu as dw', 'fp.waktu_key', '=', 'dw.waktu_key')
                ->join('dim_penduduk as dp', 'fp.penduduk_key', '=', 'dp.penduduk_key')
                ->whereNotNull('fp.umur')
                ->selectRaw("
                    FLOOR(fp.umur/5) * 5 AS bucket_start,
                    dp.jenis_kelamin,
                    SUM(fp.jumlah_penduduk) AS jumlah
                ")
                ->groupByRaw('FLOOR(fp.umur / 5) * 5, dp.jenis_kelamin');
            
            if ($tahun) {
                $query->where('dw.tahun', $tahun);
            }

            $rows = $query->get();

            $labels = [];
            for ($b=0; $b <= 70 ; $b += 5) { 
                $labels[] = $b . '-' . ($b + 4);
            }
            $labels[] = '75+';

            $lakiLaki = array_fill(0, count($labels), 0);
            $perempuan = array_fill(0, count($labels), 0);

            foreach ($rows as $row) {
                $bucket = (int) $row->bucket_start;
                $idx = $bucket >= 75 ? count($labels) - 1: intdiv($bucket, 5);
                if ($idx < 0 || $idx >= count($labels)) {
                    continue;
                }

                if ($row->jenis_kelamin === 'P') {
                    $perempuan[$idx] += (int) $row->jumlah;
                } else {
                    $lakiLaki[$idx] += (int) $row->jumlah;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data Piramida Penduduk Berhasil diambil',
                'data' => [
                    'labels'    => $labels,
                    'laki_laki' => $lakiLaki,
                    'perempuan' => $perempuan,
                    'total_laki_laki'   => array_sum($lakiLaki),
                    'total_perempuan'   => array_sum($perempuan),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response() -> json ([
                'success' => false,
                'message' => "Gagal Mengambil data piramida penduduk: " . $e->getMessage(),
            ], 500);
        }
    }

    public function getStatusPerkawinan(Request $request): JsonResponse {
        try {
            $tahun = $request->input('tahun');

            $query = DB::table('fact_penduduk as fp')
                ->join('dim_waktu as dw', 'fp.waktu_key', '=', 'dw.waktu_key')
                ->join('dim_status_perkawinan as dsp', 'fp.status_perkawinan_key', '=', 'dsp.status_perkawinan_key')
                ->select('dsp.status_perkawinan', DB::raw('count(fp.jumlah_penduduk) as jumlah'))
                ->groupBy('dsp.status_perkawinan');

                if ($tahun) {
                    $query->where('dw.tahun', $tahun);
                }

                $data = $query->orderByDesc('jumlah')->get();

                return response()->json([
                    'success' => true,
                    'message' => 'Data Status Perkawinan Berhasil Diambil',
                    'data' => $data->map(fn($r) => [
                        'status' => $r->status_perkawinan,
                        'jumlah' => (int) $r->jumlah,
                    ])->values(),
                ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data status perkawinan: ' . $e->getMessage(),
            ], 500);
        }
    }

    
}