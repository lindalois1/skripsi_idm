<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Jika sudah login, redirect ke beranda
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role == 'super_admin') {
                return redirect()->route('super.beranda');
            }
            return redirect()->route('beranda');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'role' => 'nullable|in:desa,kecamatan,kabupaten,super_admin'
        ]);

        // 🔥 DEBUG: Log login attempt
        Log::info('Login attempt', [
            'email' => $request->email,
            'role' => $request->role,
            'ip' => $request->ip()
        ]);

        // Coba login dengan email
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // 🔥 Cek apakah user aktif
            if (!$user->is_active) {
                Auth::logout();
                Log::warning('Login failed: User inactive', ['email' => $request->email]);
                return back()->withErrors([
                    'email' => 'Akun Anda tidak aktif. Silakan hubungi admin.'
                ])->onlyInput('email', 'role');
            }

            // Jika form mengirim role, pastikan sesuai dengan akun di database.
            $isSuperAdminMatch = ($user->role === 'super_admin' && in_array($request->role, ['super_admin', 'kabupaten']));
            if ($request->filled('role') && $user->role !== $request->role && !$isSuperAdminMatch) {
                Auth::logout();
                Log::warning('Login failed: Role mismatch', [
                    'user_role' => $user->role,
                    'request_role' => $request->role
                ]);
                return back()->withErrors([
                    'role' => 'Role yang dipilih tidak sesuai dengan akun Anda. Silakan pilih level pengguna yang benar.'
                ])->onlyInput('email', 'role');
            }

            $request->session()->regenerate();

            // 🔥 Update last_login
            $user->last_login = now();
            $user->save();

            Log::info('Login success', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role
            ]);

            // Redirect berdasarkan role
            if ($user->role == 'super_admin') {
                return redirect()->route('super.beranda');
            }

            return redirect()->intended('/beranda');
        }

        Log::warning('Login failed: Invalid credentials', ['email' => $request->email]);
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email', 'role');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        Log::info('Logout', ['user_id' => $user->id ?? null, 'email' => $user->email ?? null]);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}