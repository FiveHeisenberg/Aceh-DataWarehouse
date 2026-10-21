<?php

namespace App\Http\Controllers\Kesehatan;

use App\Http\Controllers\Controller;

class KesehatanController extends Controller
{
    public function rumahSakit()
    {
        return view('Kesehatan.rumah-sakit');
    }

    public function penyakit()
    {
        return view('Kesehatan.penyakit');
    }

    public function tenagaMedis()
    {
        return view('Kesehatan.tenaga-medis');
    }
}