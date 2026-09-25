<?php

namespace App\Http\Controllers\Dispenda;

use App\Http\Controllers\Controller;
use App\Models\Dispenda\DimWajibPajak;
use Illuminate\Http\Request;

class DispendaApiController extends Controller
{
    public function getYears()
    {
        $years = DimWajibPajak::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return response()->json([
            'success' => true,
            'data' => $years,
        ]);
    }

    public function getIndex(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 25);

        $query = DimWajibPajak::tahun($tahun);

        if ($search) {
            $query->cari($search);
        }

        $data = $query->orderBy('jumlah_wajib_pajak', 'desc')
            ->paginate($perPage);

        $summary = [
            'total_wajib_pajak' => DimWajibPajak::tahun($tahun)->sum('jumlah_wajib_pajak'),
            'total_kabupaten' => DimWajibPajak::tahun($tahun)->count(),
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'data' => $data,
        ]);
    }

    public function getDetail($kodeKabupaten, Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));

        $data = DimWajibPajak::kodeKabupaten($kodeKabupaten)
            ->tahun($tahun)
            ->first();

        if (! $data) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function getTren(Request $request)
    {
        $kodeKab = $request->input('kode_kab');
        $tahunMulai = $request->input('tahun_mulai', date('Y') - 5);
        $tahunAkhir = $request->input('tahun_akhir', date('Y'));

        $query = DimWajibPajak::whereBetween('tahun', [$tahunMulai, $tahunAkhir])
            ->orderBy('tahun', 'asc');

        if ($kodeKab) {
            $query->kodeKabupaten($kodeKab);
        }

        $data = $query->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
