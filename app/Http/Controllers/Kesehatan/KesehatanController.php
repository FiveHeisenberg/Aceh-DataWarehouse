<?php

namespace App\Http\Controllers\Kesehatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KesehatanController extends Controller
{
    public function index()
    {
        // Ini akan memanggil file jumlah_puskesmas.blade.php milikmu
        return view('Kesehatan.jumlah_puskesmas');
    }
}
