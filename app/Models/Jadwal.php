<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
protected $fillable = [
        'nama_workout',
        'kategori',
        'waktu_mulai',
        'waktu_selesai',
        'hari',
    ];}
