<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

// use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    // use HasFactory, Notifiable;
    use HasFactory;

    /**
     * tabel autentikasi berada di database terpisah dari data warehouse.
     */
    protected $connection = 'db_auth';

    protected $table = 'tb_user';

    protected $primaryKey = 'id_user';

    /**
     * tb_user tidak memiliki kolom created_at /update_at.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * var list<string>  @
     */
    protected $fillable = [
        'username',
        'nama_lengkap',
        'nomor_telepon',
        'email',
        'id_role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * var list<string>  @
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * return array<string, string>  @
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    /**
     * Perbandingan case-insensitive karena collation tb_role bersifat *_ci.
     */
    public function hasRole(string $jenisUser): bool
    {
        $role = $this->relationLoaded('role')
        ? $this->getRelation('role')
        : $this->role()->first();

        return $role !== null
            && strcasecmp((string) $role->jenis_user, $jenisUser) === 0;
    }
}
