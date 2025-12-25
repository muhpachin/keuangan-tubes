<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Utang extends Model
{
    protected $table = 'utang';
    protected $guarded = ['id'];
    protected $fillable = [
        'user_id',
        'pemberi',
        'deskripsi',
        'jumlah',
        'sisa_jumlah',
        'keterangan',
        'tanggal',
        'status',
        'jatuh_tempo'
    ];
    public $timestamps = false;
}
