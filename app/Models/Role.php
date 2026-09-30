<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $connection = 'db_auth';
    protected $table = 'tb_role';
    protected $primaryKey = 'id_role';
    public $timestamps = false;

    protected $fillable = [
        'jenis_user',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_role', 'id_role');
    }
}