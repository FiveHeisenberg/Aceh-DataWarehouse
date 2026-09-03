<?php

namespace App\Models\Penduduk;

use Illuminate\Database\Eloquent\Model;

class KartuKeluarga extends Model
{
    protected $table ='kartu_keluarga';

    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'kode_provinsi',
        'nama_provinsi',
        'kode_kabupaten_kota',
        'nama_kabupaten_kota',
        'tahun',
        'jumlah_kartu_keluarga',
        'satuan',
        'source',
        'loaded_at',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'jumlah_kartu_keluarga' => 'integer',
        'loaded_at' => 'datetime',
    ];

    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function scopeCari($query, $keyword)
    {
        return $query->where('nama_kabupaten_kota', 'like', '%' . $keyword . '%');
    }

    public function scopeKodeKabupaten($query, $kode)
    {
        return $query->where('kode_kabupaten_kota', $kode);
    }
}
