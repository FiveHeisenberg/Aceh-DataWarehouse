<?php

namespace App\Models\Dispenda;

use Illuminate\Database\Eloquent\Model;

class DimMetodeBayar extends Model
{
    protected $table = 'dim_metode_bayar';

    protected $primaryKey = 'metode_bayar_key';

    protected $keyType = 'int';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'metode_bayar_key',
        'metode_bayar',
    ];

    protected $casts = [
        'metode_bayar_key' => 'integer',
    ];
}