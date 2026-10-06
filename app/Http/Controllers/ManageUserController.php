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
    public function index(Request $request)
    {
        $sortBy = $request->query('sort_by', 'created_id_asc');

        $query = User::with('role');

        switch ($sortBy) {
            case 'nama_asc':
                $query->orderBy('nama_lengkap', 'asc');
                break;
            case 'created_id_asc':
                $query->orderBy('id_user', 'asc');
                break;
            case 'role_admin_first':
                $query->orderByRaw("CASE WHEN id_role = (SELECT id_role FROM db_auth.tb_role WHERE jenis_user = 'admin' LIMIT 1) THEN 0 ELSE 1 END");
                break;
        }

        $users = $query->get();
        $roles = Role::all();

        return view('manage-user', [
            'user' => auth()->user(),
            'users' => $users,
            'roles' => $roles,
            'currentSort' => $sortBy
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
                    'required',
                    'string',
                    'min:3',
                    'max:50',
                    'regex:/^[A-Za-z0-9._-]+$/',
                    Rule::unique('db_auth.tb_user', 'username')->ignore($user->id_user,'id_user'),
                ],
                'nama_lengkap' => [
                    'required', 'string', 'max:100'
                ],
                'email' => [
                    'required',
                    'string',
                    'email:rfc',
                    'max:100',
                    Rule::unique('db_auth.tb_user', 'email')->ignore($user->id_user, 'id_user'),
                ],
                'nomor_telepon' => [
                    'nullable', 'string', 'max:20'
                ],
                'id_role' => [
                    'required', 'exists:db_auth.tb_role,id_role'
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

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diperbarui.'
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus user'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => [
                'required', 'string', 'min:3', 'max:50',
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('db_auth.tb_user', 'username')
            ],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'email' => [
                'required', 'email:rfc', 'max:100',
                Rule::unique('db_auth.tb_user', 'email')
            ],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
            'id_role' => ['required', 'exists:db_auth.tb_role,id_role'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 50 karakter.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda kurung.',
            'username.unique' => 'Username sudah digunakan.',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi.',
            'nama_lengkap.max' => 'Nama Lengkap maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',
            'email.unique' => 'Email sudah digunakan.',
            'nomor_telepon.max' => 'Nomor telepon maksimal 20 karakter.',
            'id_role.required' => 'Role wajib dipilih.',
            'id_role.exists' => 'Role tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'username' => $validated['username'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'password' => bcrypt($validated['password']),
            'id_role' => $validated['id_role'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menambah user'
        ]);
    }
}