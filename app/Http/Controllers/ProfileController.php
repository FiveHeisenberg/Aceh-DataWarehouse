<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile', [
            'user' => $request->user()->load('role'),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['username', 'nama_lengkap', 'nomor_telepon', 'email']);

        $data['nomor_telepon'] = ($data['nomor_telepon'] ?? '') !== ''
            ? $data['nomor_telepon']
            : null;

        $request->user()->fill($data)->save();

        return back()->with('success', 'Data profile berhasil diperbarui.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
