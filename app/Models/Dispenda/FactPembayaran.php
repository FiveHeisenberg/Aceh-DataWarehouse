<?php

namespace App\Models\Dispenda;

use Illuminate\Database\Eloquent\Model;

class FactPembayaran extends Model
{
    protected $table = 'fact_pembayaran';

    protected $primaryKey = 'fact_pembayaran_key';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'fact_pembayaran_key',
        'fact_tagihan_key',
        'metode_bayar_key',
        'waktu_bayar_key',
        'id_pembayaran',
        'tanggal_bayar',
        'jumlah_bayar',
        'denda_keterlambatan',
        'total_bayar',
    ];

    protected $casts = [
        'fact_pembayaran_key' => 'integer',
        'fact_tagihan_key' => 'integer',
        'metode_bayar_key' => 'integer',
        'waktu_bayar_key' => 'integer',
        'jumlah_bayar' => 'integer',
        'denda_keterlambatan' => 'integer',
        'total_bayar' => 'integer',
        'tanggal_bayar' => 'date',
    ];

    /**
     * Tahun pembayaran diturunkan dari dim_waktu lewat waktu_bayar_key,
     * karena fact_pembayaran tidak menyimpan kolom tahun langsung.
     */
    public function scopeTahun($query, $tahun)
    {
        return $query->whereHas('waktu', fn ($q) => $q->where('tahun', $tahun));
    }

    public function tagihan()
    {
        return $this->belongsTo(FactTagihan::class, 'fact_tagihan_key', 'fact_tagihan_key');
    }

    public function waktu()
    {
        return $this->belongsTo(DimWaktu::class, 'waktu_bayar_key', 'waktu_key');
    }

    public function metodeBayar()
    {
        return $this->belongsTo(DimMetodeBayar::class, 'metode_bayar_key', 'metode_bayar_key');
    }
}