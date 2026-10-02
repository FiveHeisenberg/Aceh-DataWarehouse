<?php

namespace App\Models\Dispenda;

use Illuminate\Database\Eloquent\Model;

class DimKategoriPajak extends Model
{
    protected $table = 'dim_kategori_pajak';

    protected $primaryKey = 'kategori_pajak_key';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'kategori_pajak_key',
        'id_kategori',
        'nama_pajak',
        'tarif_persentase',
    ];

    protected $casts = [
        'kategori_pajak_key' => 'integer',
        'id_kategori' => 'integer',
        'tarif_persentase' => 'decimal:2',
    ];

    public function tagihan()
    {
        return $this->hasMany(FactTagihan::class, 'kategori_pajak_key', 'kategori_pajak_key');
    }
}