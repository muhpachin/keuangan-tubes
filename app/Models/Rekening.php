<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    protected $table = 'rekening'; // Nama tabel di SQL
    protected $guarded = ['id'];   // Semua kolom bisa diisi kecuali ID

    public function user() { return $this->belongsTo(User::class); }
}
