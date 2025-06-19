<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    protected $fillable = [
        'judul',
        'gambar_url',
        'kategori',
        'video_url',
        'instruksi',
    ];
}
