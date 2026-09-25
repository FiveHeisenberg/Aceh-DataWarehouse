<?php

namespace App\Http\Controllers\Dispenda;

use App\Http\Controllers\Controller;

class DispendaController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Dispenda',
            'total_wajib_pajak' => 0,
            'tahun' => 2024,
            'pertumbuhan' => 0,
        ];

        return view('dispenda.index', compact('data'));
    }
}
