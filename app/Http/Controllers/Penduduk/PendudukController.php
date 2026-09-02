<?php

namespace App\Http\Controllers\Penduduk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PendudukController extends Controller
{
    public function index()
    {
        // Contoh data - nanti bisa diganti dengan data dari database
        $data = [
            'title' => 'Jumlah Penduduk',
            'total_penduduk' => 5274875,
            'tahun' => 2024,
            'pertumbuhan' => 1.2,
        ];

        return view('penduduk.jumlah_penduduk', compact('data'));
    }
}