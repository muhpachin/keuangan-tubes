<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    
    public function login(Request $request) {
        // Cek username & password
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->route('dashboard');
        }
        return back()->with('error', 'Username atau password salah');
    }

    public function showRegister() { return view('auth.register'); }

    public function register(Request $request) {
        $request->validate([
            'username' => 'required|unique:users',
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'security_question' => 'required',
            'security_answer' => 'required'
        ]);

        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'security_question' => $request->security_question,
            'security_answer' => $request->security_answer,
            'tipe_akun' => 'gratis'
        ]);

        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Selamat datang! Akun berhasil dibuat.');
    }

    public function redirectToGoogle() { return Socialite::driver('google')->redirect(); }

    public function handleGoogleCallback() {
        try {
            $googleUser = Socialite::driver('google')->user();
            // Cek berdasarkan google_id atau email
            $user = User::where('google_id', $googleUser->getId())
                        ->orWhere('email', $googleUser->getEmail())
                        ->first();

            if (!$user) {
                // Register User Baru
                $user = User::create([
                    'username' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'tipe_akun' => 'gratis',
                    'password' => null, 
                ]);
            } else {
                // Update Google ID jika belum ada
                if (!$user->google_id) $user->update(['google_id' => $googleUser->getId()]);
            }

            Auth::login($user);
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login Google Gagal.');
        }
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('login');
    }
}
