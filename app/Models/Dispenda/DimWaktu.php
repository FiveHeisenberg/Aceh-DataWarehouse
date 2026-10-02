<?php

namespace App\Models\Dispenda;

use Illuminate\Database\Eloquent\Model;

class DimWaktu extends Model
{
    protected $table = 'dim_waktu';

    protected $primaryKey = 'waktu_key';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'waktu_key',
        'tanggal',
        'hari',
        'bulan',
        'nama_bulan',
        'kuartal',
        'tahun',
    ];

    protected $casts = [
        'waktu_key' => 'integer',
        'tahun' => 'integer',
        'bulan' => 'integer',
        'kuartal' => 'integer',
        'tanggal' => 'date',
    ];
}