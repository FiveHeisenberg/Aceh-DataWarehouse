<?php

namespace App\Models\Penduduk;

use Illuminate\Database\Eloquent\Model;

class JumlahPenduduk extends Model
{
    // Nama tabel di database
    protected $table = 'jumlah_penduduk';

    // Primary key
    protected $primaryKey = 'id';

    // Tipe primary key
    protected $keyType = 'int';
    public $incrementing = true;

    // Nonaktifkan timestamps default (created_at, updated_at)
    public $timestamps = false;

    // Kolom yang boleh diisi massal
    protected $fillable = [
        'kode_provinsi',
        'nama_provinsi',
        'kode_kabupaten_kota',
        'nama_kabupaten_kota',
        'tahun',
        'jumlah_penduduk',
        'satuan',
        'source',
        'loaded_at',
    ];

    // Casting tipe data
    protected $casts = [
        'tahun' => 'integer',
        'jumlah_penduduk' => 'integer',
        'loaded_at' => 'datetime',
    ];

    // ==================== SCOPES ====================
    
    /**
     * Scope: Filter berdasarkan tahun
     */
    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    /**
     * Scope: Filter berdasarkan kode kabupaten
     */
    public function scopeKodeKabupaten($query, $kode)
    {
        return $query->where('kode_kabupaten_kota', $kode);
    }

    /**
     * Scope: Pencarian nama kabupaten/kota
     */
    public function scopeCari($query, $keyword)
    {
        return $query->where('nama_kabupaten_kota', 'like', '%' . $keyword . '%');
    }

    // ==================== RELATIONSHIPS ====================

    /**
     * Get data tahun sebelumnya (untuk perhitungan pertumbuhan)
     */
    public function tahunSebelumnya()
    {
        return $this->hasOne(self::class, 'kode_kabupaten_kota', 'kode_kabupaten_kota')
                    ->where('tahun', \DB::raw('tahun - 1'));
    }
}