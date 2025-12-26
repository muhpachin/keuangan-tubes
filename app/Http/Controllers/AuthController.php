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

    // Tampilkan form lupa password
    public function showForgotForm()
    {
        return view('auth.forgot');
    }

    // Proses kirim (tampilkan) pertanyaan keamanan
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string', // username atau email
        ]);

        $identifier = $request->identifier;
        $user = User::where('username', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        if (!$user->security_question) {
            return back()->with('error', 'Akun tidak memiliki pertanyaan keamanan terdaftar.');
        }

        return view('auth.forgot_question', ['user' => $user]);
    }

    // Verifikasi jawaban keamanan dan reset password
    public function verifySecurityAnswer(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'security_answer' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::find($request->user_id);
        if (!$user) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        $given = strtolower(trim($request->security_answer));
        if (!Hash::check($given, $user->security_answer)) {
            return back()->with('error', 'Jawaban keamanan salah.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login.');
    }

    // Tampilkan form reset password (menggunakan token)
    public function showResetForm($token)
    {
        // Find candidate users with non-expired tokens, then verify the hashed token
        $candidates = User::whereNotNull('reset_token')
            ->whereNotNull('reset_token_expiry')
            ->where('reset_token_expiry', '>=', Carbon::now())
            ->get();

        $found = null;
        foreach ($candidates as $u) {
            if (Hash::check($token, $u->reset_token)) {
                $found = $u;
                break;
            }
        }

        if (!$found) {
            return redirect()->route('password.request')->with('error', 'Token tidak valid atau sudah kadaluarsa.');
        }

        return view('auth.reset', ['token' => $token]);
    }

    // Proses reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ]);

        // Locate user by verifying hashed reset_token among non-expired candidates
        $candidates = User::whereNotNull('reset_token')
            ->whereNotNull('reset_token_expiry')
            ->where('reset_token_expiry', '>=', Carbon::now())
            ->get();

        $found = null;
        foreach ($candidates as $u) {
            if (Hash::check($request->token, $u->reset_token)) {
                $found = $u;
                break;
            }
        }

        if (!$found) {
            return redirect()->route('password.request')->with('error', 'Token tidak valid atau sudah kadaluarsa.');
        }

        $found->password = Hash::make($request->password);
        $found->reset_token = null;
        $found->reset_token_expiry = null;
        $found->save();

        return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login.');
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
        $user = User::create([
            'username' => $request->username, // Pakai username
            // 'name' => $request->name, <--- BARIS INI SUDAH DIHAPUS AGAR TIDAK ERROR
            'password' => Hash::make($request->password),
            'security_question' => $request->security_question,
            // Hash jawaban keamanan agar aman
            'security_answer' => Hash::make(strtolower(trim($request->security_answer))),
            'tipe_akun' => 'gratis', // Set default tipe akun
        ]);

        // Seed default categories for this user
        if (class_exists(\App\Models\DefaultCategory::class)) {
            $defaults = \App\Models\DefaultCategory::all();
            foreach ($defaults as $d) {
                if ($d->type === 'pemasukan' && class_exists(\App\Models\Kategori::class)) {
                    \App\Models\Kategori::firstOrCreate([
                        'user_id' => $user->id,
                        'nama_kategori' => $d->name,
                    ]);
                } elseif ($d->type === 'pengeluaran' && class_exists(\App\Models\KategoriPengeluaran::class)) {
                    \App\Models\KategoriPengeluaran::firstOrCreate([
                        'user_id' => $user->id,
                        'nama_kategori' => $d->name,
                    ]);
                }
            }
        }

        // Setelah registrasi berhasil, arahkan ke halaman login dengan pemberitahuan sukses
        return redirect()->route('login')->with('success', 'Akun berhasil dibuat. Silakan login.');
    }

    // Proses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Prevent banned users
        $candidate = User::where('username', $request->username)->first();
        if ($candidate && $candidate->is_banned) {
            return back()->with('error', 'Akun ini diblokir. Hubungi administrator.');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Update last login timestamp
            $user = Auth::user();
            $user->last_login_at = now();
            $user->save();

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
        if (!class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
            return redirect()->route('login')->with('error', 'Google login tidak tersedia.');
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        if (!class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
            return redirect()->route('login')->with('error', 'Google login tidak tersedia.');
        }

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
