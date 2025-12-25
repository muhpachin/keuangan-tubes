<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // Menampilkan Halaman Login
    public function showLogin() { 
        return view('auth.login'); 
    }

    // Menampilkan Halaman Register
    public function showRegister() { 
        return view('auth.register'); 
    }

    // Proses Register (DIPERBAIKI: Menghapus 'name', menggunakan 'username')
    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'username' => 'required|unique:users', // Cek unik username
            'password' => 'required|min:6',
            'security_question' => 'required',
            'security_answer' => 'required',
        ]);

        // 2. Simpan ke Database
        User::create([
            'username' => $request->username, // Pakai username
            // 'name' => $request->name, <--- BARIS INI SUDAH DIHAPUS AGAR TIDAK ERROR
            'password' => Hash::make($request->password),
            'security_question' => $request->security_question,
            // Hash jawaban keamanan agar aman
            'security_answer' => Hash::make(strtolower(trim($request->security_answer))),
            'tipe_akun' => 'gratis', // Set default tipe akun
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // Proses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Username atau password salah.');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // --- GOOGLE LOGIN ---
    public function redirectToGoogle() { 
        return Socialite::driver('google')->redirect(); 
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Cek user berdasarkan Google ID atau Email
            $user = User::where('google_id', $googleUser->getId())
                        ->orWhere('email', $googleUser->getEmail())
                        ->first();

            if (!$user) {
                // Register User Baru dari Google
                $user = User::create([
                    'username' => $googleUser->getName(), // Gunakan nama Google sebagai username awal
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'tipe_akun' => 'gratis',
                    'password' => null, // User Google tidak butuh password
                ]);
            } else {
                // Jika user ada tapi belum link Google ID
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->getId()]);
                }
            }

            Auth::login($user);
            return redirect()->route('dashboard');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login Google Gagal: ' . $e->getMessage());
        }
    }
}