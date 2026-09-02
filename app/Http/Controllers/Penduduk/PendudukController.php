<?php
// app/Http/Controllers/Penduduk/PendudukController.php

namespace App\Http\Controllers\Penduduk;

use App\Http\Controllers\Controller;

class PendudukController extends Controller
{
    public function index()
    {
        return view('penduduk.index');
    }

    public function jumlah()
    {
        return view('penduduk.jumlah_penduduk');
    }

    public function kepadatan()
    {
        return view('penduduk.kepadatan');
    }

    public function pertumbuhan()
    {
        return view('penduduk.pertumbuhan');
    }
}