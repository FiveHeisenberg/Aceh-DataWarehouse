<?php

namespace App\Http\Controllers\Penduduk;

use App\Http\Controllers\Controller;
use App\Models\Penduduk\JumlahPenduduk;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PendudukApiController extends Controller
{
    /**
     * GET /api/penduduk/years
     * Endpoint: Ambil daftar tahun yang tersedia
     */
    public function getYears(): JsonResponse
    {
        try {
            $years = JumlahPenduduk::select('tahun')
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->pluck('tahun');

            return response()->json([
                'success' => true,
                'message' => 'Daftar tahun berhasil diambil',
                'data' => $years
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar tahun',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/penduduk/index?tahun=2023&search=banda
     * Endpoint: Ambil summary + detail data penduduk
     */
    public function getIndex(Request $request): JsonResponse
    {
        try {
            // Validasi input
            $request->validate([
                'tahun' => 'nullable|integer|min:2000|max:' . date('Y'),
                'search' => 'nullable|string|max:100',
                'kode_kab' => 'nullable|string|max:10',
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            // Ambil tahun (default: tahun terbaru)
            $tahun = $request->input('tahun', JumlahPenduduk::max('tahun'));
            $search = $request->input('search', '');
            $kodeKab = $request->input('kode_kab', '');
            $perPage = $request->input('per_page', 25);

            // --- A. SUMMARY STATISTICS ---
            $totalPenduduk = JumlahPenduduk::tahun($tahun)->sum('jumlah_penduduk');
            
            $tahunSebelumnya = $tahun - 1;
            $totalTahunLalu = JumlahPenduduk::tahun($tahunSebelumnya)->sum('jumlah_penduduk');

            $pertumbuhan = 0;
            if ($totalTahunLalu > 0) {
                $pertumbuhan = (($totalPenduduk - $totalTahunLalu) / $totalTahunLalu) * 100;
            }

            // Hitung jumlah kabupaten/kota
            $jumlahKabupaten = JumlahPenduduk::tahun($tahun)
                ->distinct('kode_kabupaten_kota')
                ->count('kode_kabupaten_kota');

            // --- B. DETAIL DATA (PAGINATED) ---
            $query = JumlahPenduduk::tahun($tahun);

            if (!empty($search)) {
                $query->cari($search);
            }

            if (!empty($kodeKab)) {
                $query->kodeKabupaten($kodeKab);
            }

            $details = $query->orderBy('jumlah_penduduk', 'desc')
                ->paginate($perPage);

            // --- C. DATA UNTUK CHART TREN (3 tahun terakhir) ---
            $trenData = JumlahPenduduk::select('tahun', DB::raw('SUM(jumlah_penduduk) as total'))
                ->whereIn('tahun', [$tahun - 2, $tahun - 1, $tahun])
                ->groupBy('tahun')
                ->orderBy('tahun', 'asc')
                ->get();

            // --- D. RESPONSE ---
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diambil',
                'data' => [
                    'tahun_aktif' => (int) $tahun,
                    'summary' => [
                        'total_penduduk' => (int) $totalPenduduk,
                        'pertumbuhan_persen' => round($pertumbuhan, 2),
                        'total_tahun_lalu' => (int) $totalTahunLalu,
                        'jumlah_kabupaten_kota' => (int) $jumlahKabupaten
                    ],
                    'details' => [
                        'data' => $details->items(),
                        'current_page' => $details->currentPage(),
                        'last_page' => $details->lastPage(),
                        'per_page' => $details->perPage(),
                        'total' => $details->total(),
                    ],
                    'tren' => $trenData->map(function($item) {
                        return [
                            'tahun' => (int) $item->tahun,
                            'total' => (int) $item->total
                        ];
                    })
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/penduduk/detail/{kode_kabupaten}
     * Endpoint: Ambil detail data per kabupaten/kota
     */
    public function getDetail(string $kodeKabupaten): JsonResponse
    {
        try {
            $data = JumlahPenduduk::where('kode_kabupaten_kota', $kodeKabupaten)
                ->orderBy('tahun', 'desc')
                ->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan untuk kode kabupaten: ' . $kodeKabupaten
                ], 404);
            }

            // Hitung pertumbuhan per tahun
            $dataWithGrowth = $data->map(function($item, $index) use ($data) {
                $pertumbuhan = 0;
                if ($index < $data->count() - 1) {
                    $tahunLalu = $data[$index + 1]->jumlah_penduduk;
                    if ($tahunLalu > 0) {
                        $pertumbuhan = (($item->jumlah_penduduk - $tahunLalu) / $tahunLalu) * 100;
                    }
                }

                return [
                    'tahun' => $item->tahun,
                    'jumlah_penduduk' => $item->jumlah_penduduk,
                    'pertumbuhan_persen' => round($pertumbuhan, 2),
                    'satuan' => $item->satuan
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Detail data berhasil diambil',
                'data' => [
                    'kode_kabupaten' => $kodeKabupaten,
                    'nama_kabupaten' => $data->first()->nama_kabupaten_kota,
                    'provinsi' => $data->first()->nama_provinsi,
                    'histori' => $dataWithGrowth
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/penduduk/tren?kode_kab=1101&tahun_mulai=2020&tahun_akhir=2023
     * Endpoint: Ambil data tren untuk chart
     */
    public function getTren(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'kode_kab' => 'nullable|string|max:10',
                'tahun_mulai' => 'nullable|integer|min:2000',
                'tahun_akhir' => 'nullable|integer|max:' . date('Y'),
            ]);

            $kodeKab = $request->input('kode_kab');
            $tahunMulai = $request->input('tahun_mulai', 2020);
            $tahunAkhir = $request->input('tahun_akhir', date('Y'));

            $query = JumlahPenduduk::select('tahun', DB::raw('SUM(jumlah_penduduk) as total'))
                ->whereBetween('tahun', [$tahunMulai, $tahunAkhir]);

            if (!empty($kodeKab)) {
                $query->where('kode_kabupaten_kota', $kodeKab);
            }

            $trenData = $query->groupBy('tahun')
                ->orderBy('tahun', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data tren berhasil diambil',
                'data' => $trenData->map(function($item) {
                    return [
                        'tahun' => (int) $item->tahun,
                        'total' => (int) $item->total
                    ];
                })
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tren',
                'error' => $e->getMessage()
            ], 500);
        }
    }


        /**
     * GET /api/penduduk/map?tahun=2023
     * Endpoint khusus untuk data peta (choropleth)
     */
    public function getMapData(Request $request): JsonResponse
    {
        try {
            // Ambil tahun dari request, default ke tahun terbaru
            $tahun = $request->input('tahun', JumlahPenduduk::max('tahun'));

            // 1. Ambil data tahun saat ini
            $currentData = JumlahPenduduk::select(
                'kode_kabupaten_kota as kode',
                'nama_kabupaten_kota as nama',
                'jumlah_penduduk',
                'satuan'
            )
            ->where('tahun', $tahun)
            ->orderBy('jumlah_penduduk', 'desc')
            ->get();

            // 2. Ambil data tahun sebelumnya untuk hitung pertumbuhan
            $prevData = JumlahPenduduk::select('kode_kabupaten_kota as kode', 'jumlah_penduduk')
                ->where('tahun', $tahun - 1)
                ->get()
                ->pluck('jumlah_penduduk', 'kode'); // Key by kode

            $result = [];
            $rank = 1;

            foreach ($currentData as $row) {
                $jumlah = (int)$row->jumlah_penduduk;
                $prev = $prevData[$row->kode] ?? null;
                
                $pertumbuhan = ($prev && $prev > 0) 
                    ? round((($jumlah - $prev) / $prev) * 100, 2) 
                    : null;

                $result[] = [
                    'kode'               => $row->kode,
                    'nama'               => $row->nama,
                    'jumlah_penduduk'    => $jumlah,
                    'satuan'             => $row->satuan,
                    'peringkat'          => $rank++,
                    'pertumbuhan_persen' => $pertumbuhan,
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Data peta berhasil diambil',
                'data'    => [
                    'tahun'     => (int) $tahun,
                    'kabupaten' => $result,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data peta: ' . $e->getMessage()
            ], 500);
        }
    }
}