<?php

namespace App\Models\Dispenda;

use Illuminate\Database\Eloquent\Model;

class FactTagihan extends Model
{
    protected $table = 'fact_tagihan';

    protected $primaryKey = 'fact_tagihan_key';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'fact_tagihan_key',
        'wajib_pajak_key',
        'objek_pajak_key',
        'kategori_pajak_key',
        'waktu_key',
        'id_tagihan',
        'tahun_pajak',
        'nominal_tagihan',
        'tanggal_jatuh_tempo',
        'status_tagihan',
    ];

    protected $casts = [
        'fact_tagihan_key' => 'integer',
        'wajib_pajak_key' => 'integer',
        'objek_pajak_key' => 'integer',
        'kategori_pajak_key' => 'integer',
        'waktu_key' => 'integer',
        // varchar(4) di sumber, dipertahankan agar tidak terbuang leading zero.
        'tahun_pajak' => 'string',
        'nominal_tagihan' => 'integer',
        'tanggal_jatuh_tempo' => 'date',
    ];

    /**
     * Kolom tahun_pajak disimpan sebagai varchar di DWH, sehingga perlu
     * dibandingkan sebagai string agar aman terhadap tipe data.
     */
    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun_pajak', (string) $tahun);
    }

    public function scopeKodeKabupaten($query, $kode)
    {
        return $query->whereHas('wajibPajak', fn ($q) => $q->where('id_kabupaten_kota', $kode));
    }

    public function wajibPajak()
    {
        return $this->belongsTo(DimWajibPajak::class, 'wajib_pajak_key', 'wajib_pajak_key');
    }

    public function kategoriPajak()
    {
        return $this->belongsTo(DimKategoriPajak::class, 'kategori_pajak_key', 'kategori_pajak_key');
    }

    public function pembayaran()
    {
        return $this->hasMany(FactPembayaran::class, 'fact_tagihan_key', 'fact_tagihan_key');
    }
}