<?php

namespace App\Http\Requests;

use App\Rules\UniqueAuthTable;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->user()->getKey();

        return [
            'username' => [
                'required', 'string', 'min:3', 'max:50',
                'regex:/^[A-Za-z0-9._-]+$/', new UniqueAuthTable('tb_user', 'username', 'Username sudah digunakan.', 'db_auth', $userId),
            ],
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nomor_telepon' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
            'email' => [
                'required', 'string', 'email:rfc', 'max:100', new UniqueAuthTable(
                    'tb_user', 'email', 'Email sudah digunakan.', 'db_auth', $userId
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Username Wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 50 karakter.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 100 karakter.',
            'nomor_telepon.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, dan tanda + - ( ).',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 100 karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => 'username',
            'nama_lengkap' => 'nama lengkap',
            'nomor_telepon' => 'nomor telepon',
            'email' => 'email',
        ];
    }
}
