<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:100'],
        ]);

        try {
            $status = Password::sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Gagal mengirim email reset. Periksa konfigurasi email atau coba lagi nanti.',
                ]);
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Mohon tunggu sebentar sebelum meminta link reset lagi.',
                ]);
        }

        return back()->with(
            'status',
            'Jika email terdaftar, link reset password telah dikirim. Silakan periksa inbox Anda.'
        );
    }
}