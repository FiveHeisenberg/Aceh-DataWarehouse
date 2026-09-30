<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Rules\UniqueAuthTable;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Nama Role yang diberikan otomatis ke pengguna yang mendaftar.
     * Role admin hanya bisa diberikan manual oleh admin (melalui database).
     */
    private const DEFAULT_ROLE = 'User';

    public function store(Request $request): RedirectResponse
    {
        $validate = $request->validate(
            [
                'username' => [
                    'required',
                    'string',
                    'min:3',
                    'max:50',
                    'regex:/^[A-Za-z0-9._-]+$/',
                    new UniqueAuthTable('tb_user', 'username', 'Username sudah digunakan.'),
                ],

                'nama_lengkap' => ['required', 'string', 'max:100'],
                'email' => [
                    'required',
                    'string',
                    'email:rfc',
                    'max:100',
                    new UniqueAuthTable('tb_user', 'email', 'Email sudah digunakan.'),
                ],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ],
            [
                'username.required' => 'Username wajib diisi.',
                'username.min' => 'Username minimal 3 karakter.',
                'username.max' => 'Username maksimal 50 Karakter.',
                'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda kurung.',
                'nama_lengkap.required' => 'Nama Lengkap wajib diisi.',
                'nama_lengkap.max' => 'Nama lengkap maksimal 100 karakter.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.max' => 'Email maksimal 100 karakter.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
            ],
            [
                'username' => 'username',
                'nama_lengkap' => 'nama lengkap',
                'email' => 'email',
                'password' => 'password',
            ]
        );

        $role = Role::query()
            ->where('jenis_user', self::DEFAULT_ROLE)
            ->first();
        
        if ($role === null) {
            throw ValidationException::withMessages([
                'username' => 'Pendaftaran sedan tidak tersedia. Silahkan coba lagi nanti.',
            ]);
        }

        try {
            User::create([
                'username' => $validate['username'],
                'nama_lengkap' => $validate['nama_lengkap'],
                'email' => $validate['email'],
                'nomor_telepon' => null,
                'id_role' => $role->getKey(),
                'password' => $validate['password'],
            ]);
        } catch (QueryException $e) {
            //1062 = duplicate entry untuk unique index username / email.
            if (($e->errorInfo[1] ?? null) == 1062) {
                throw ValidationException::withMessages([
                    'username' => 'Username atau email sudah digunakan.',
                ]);
            }

            throw $e;
        }

        return redirect()
            ->route('login')
            ->with('status', 'Pendaftaran berhasil. silahkan masuk dengan akun Anda.');
    }
}