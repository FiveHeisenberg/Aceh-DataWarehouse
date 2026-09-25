<?php

namespace App\Models\Dispenda;

use Illuminate\Database\Eloquent\Model;

class FactPembayaranPajak extends Model
{
    protected $table = 'fact_pembayaran_pajak';

    protected $primaryKey = 'id';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'wajib_pajak_id',
        'kode_provinsi',
        'nama_provinsi',
        'kode_kabupaten_kota',
        'nama_kabupaten_kota',
        'tahun',
        'jumlah_pembayaran',
        'nominal_pembayaran',
        'satuan',
        'source',
        'loaded_at',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'jumlah_pembayaran' => 'integer',
        'nominal_pembayaran' => 'decimal:2',
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

    public function wajibPajak()
    {
        return $this->belongsTo(DimWajibPajak::class, 'wajib_pajak_id', 'id');
    }
}
