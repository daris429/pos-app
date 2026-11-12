<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek status keaktifan user
            if (isset($user->is_active) && !$user->is_active) {
                LogHelper::log('Login Gagal', "Percobaan login akun dinonaktifkan: {$user->email}");
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda dinonaktifkan, hubungi admin.'])->onlyInput('email');
            }

            $request->session()->regenerate();
            
            // Catat Log Aktivitas Login Berhasil
            LogHelper::log('Login', "User {$user->name} ({$user->role}) berhasil login ke sistem");

            return redirect()->intended(route('dashboard'));
        }

        // Catat Log Aktivitas Kredensial Salah
        LogHelper::log('Login Gagal', "Percobaan login gagal dengan email: {$request->email}");

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Catat Log Aktivitas Logout sebelum session dihancurkan
            LogHelper::log('Logout', "User {$user->name} keluar dari sistem");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}