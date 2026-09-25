<?php

namespace App\Models\Dispenda;

use Illuminate\Database\Eloquent\Model;

class DimWajibPajak extends Model
{
    protected $table = 'dim_wajib_pajak';

    protected $primaryKey = 'id';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'kode_provinsi',
        'nama_provinsi',
        'kode_kabupaten_kota',
        'nama_kabupaten_kota',
        'tahun',
        'jumlah_wajib_pajak',
        'satuan',
        'source',
        'loaded_at',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'jumlah_wajib_pajak' => 'integer',
        'loaded_at' => 'datetime',
    ];

    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function scopeKodeKabupaten($query, $kode)
    {
        return $query->where('kode_kabupaten_kota', $kode);
    }

    public function scopeCari($query, $keyword)
    {
        return $query->where('nama_kabupaten_kota', 'like', '%'.$keyword.'%');
    }

    public function pembayaranPajak()
    {
        return $this->hasMany(FactPembayaranPajak::class, 'wajib_pajak_id', 'id');
    }
}
