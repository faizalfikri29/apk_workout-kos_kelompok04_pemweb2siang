<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
        protected $fillable = [
            'nama_workout',
            'kategori',
            'waktu_mulai',
            'waktu_selesai',
            'hari',
            'nama_jadwal',
            
        ];

public function workouts()
    {
        return $this->hasMany(Workout::class, 'jadwal_id');
    }
}

    