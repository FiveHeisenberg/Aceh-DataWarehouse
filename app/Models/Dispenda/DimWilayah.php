<?php

namespace App\Models\Dispenda;

use Illuminate\Database\Eloquent\Model;

class DimWilayah extends Model
{
    protected $table = 'dim_wilayah';

    protected $primaryKey = 'wilayah_key';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'wilayah_key',
        'id_alamat',
        'jalan',
        'kode_pos',
        'id_desa',
        'nama_desa',
        'id_kecamatan',
        'nama_kecamatan',
        'id_kabupaten_kota',
        'nama_kabupaten_kota',
        'id_provinsi',
        'nama_provinsi',
    ];

    protected $casts = [
        'wilayah_key' => 'integer',
        'id_kabupaten_kota' => 'string',
        'id_provinsi' => 'string',
    ];

    public function scopeKodeKabupaten($query, $kode)
    {
        return $query->where('id_kabupaten_kota', $kode);
    }

    public function scopeCari($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama_kabupaten_kota', 'like', '%'.$keyword.'%')
                ->orWhere('nama_provinsi', 'like', '%'.$keyword.'%');
        });
    }
}