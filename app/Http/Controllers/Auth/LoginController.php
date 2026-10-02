<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);
    
        $user = User::where('username', $credentials['username'])->first();
    
        if (!$user || !password_verify($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }
    
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
    
        // NOTIFIKASI LENGKAPI DIRI, HANYA SEKALI DI AWAL SESI LOGIN
        if (blank($user->nomor_telepon)) {
            $request->session()->put('notif_lengkapi_diri', true);
        }
    
        return redirect()->intended(route('index'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}