<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Utang extends Model
{
    protected $table = 'utang';
    protected $guarded = ['id'];
    public $timestamps = false;
}
