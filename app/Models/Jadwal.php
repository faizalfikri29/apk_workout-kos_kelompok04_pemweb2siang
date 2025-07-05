<?php
// File: app/Models/Jadwal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jadwal extends Model
{
    use HasFactory;

    /**
     * Sesuaikan dengan kolom yang baru di migrasi.
     */
    protected $fillable = [
        'nama_jadwal',
        'hari',
        'deskripsi',
    ];

    /**
     * Relasi ini sudah benar. Satu Jadwal punya banyak Workout.
     */
    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class, 'jadwal_id');
    }

    public function workoutLogs()
{
    return $this->hasMany(WorkoutLog::class);
}

}