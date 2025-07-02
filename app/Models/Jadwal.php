<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    // Sesuaikan dengan kolom yang ada di migrasi
    protected $fillable = [
        'nama_workout',
        'kategori',
        'hari',
        'waktu_mulai',
        'waktu_selesai',
        // 'nama_jadwal', // Hapus ini jika tidak ada kolomnya di database
    ];

    public function workouts()
    {
        return $this->hasMany(Workout::class, 'jadwal_id');
    }
}
