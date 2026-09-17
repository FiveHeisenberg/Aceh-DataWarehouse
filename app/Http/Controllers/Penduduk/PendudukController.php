<?php

namespace App\Http\Controllers\Penduduk;

use App\Http\Controllers\Controller;

class PendudukController extends Controller
{
    public function index()
    {
        return view('penduduk.jumlah_penduduk');
    }
}