<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Rules\UniqueAuthTable;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ManageUserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();

        return view('manage-user', [
            'user' => auth()->user(),
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        
        return response()->json([
            'success' => true,
            'user' => $user,
            'roles' => $roles
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validate = $request->validate(
            [
                'username' => [
                    'require',
                    'string',
                    'min:3',
                    'max:50',
                    'regex:/^[A-Za-z0-9._-]+$/',
                    Rule::unique('tb_user', 'username')->ignore($user->id_user,'id_user'),
                ],
                'nama_lengkap' => [
                    'required', 'string', 'max:100'
                ],
                'email' => [
                    'require',
                    'string',
                    'email:rfc',
                    'max:100',
                    Rule::unique('tb_user', 'email')->ignore($user->id_user, 'id_user'),
                ],
                'nomor_telepon' => [
                    'nullable', 'string', 'max:20'
                ],
                'id_role' => [
                    'required', 'exists:tb_role,id_role'
                ],
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
                'nomor_telepon.max' => 'Nomor telepon maksimal 20 karakter.',
                'id_role.required' => 'Role wajib dipilih.',
                'id_role.exists' => 'Role tidak valid.',

            ]
        );

        try {
            $user->update([
                'username' => $validate['username'],
                'nama_lengkap' => $validate['nama_lengkap'],
                'email' => $validate['email'],
                'nomor_telepon' => $validate['nomor_telepon'] ?? null,
                'id_role' => $validate['id_role'],
            ]);
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) == 1062) {
                throw ValidationException::withMessages([
                    'username' => 'Username atau email sudah digunakan.',
                ]);
            }
            throw $e;
        }

        return redirect()
            ->route('manage-user')
            ->with('success', 'Data user berhasil diperbarui.');
    }
}