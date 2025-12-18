<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'pengeluaran';
    protected $guarded = ['id'];
    public $timestamps = false; // Sesuai SQL

    public function rekening() { return $this->belongsTo(Rekening::class); }
}