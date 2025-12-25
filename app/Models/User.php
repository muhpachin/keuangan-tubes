<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Sesuai kolom di tabel 'users'
    protected $fillable = [
        'name', 'username', 'email', 'password', 'google_id', 
        'security_question', 'security_answer', 'tipe_akun', 
        'fcm_token', 'reset_token', 'reset_token_expiry'
    ];

    protected $hidden = ['password', 'remember_token', 'security_answer'];

    // Relasi
    public function rekening() { return $this->hasMany(Rekening::class); }
    public function pemasukan() { return $this->hasMany(Pemasukan::class); }
    public function pengeluaran() { return $this->hasMany(Pengeluaran::class); }
}