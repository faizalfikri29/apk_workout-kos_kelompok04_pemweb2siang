<?php
// File: app/Models/Workout.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workout extends Model
{
    use HasFactory;

    protected $fillable = [
        'jadwal_id',
        'nama_workout',
        'name',
        'kategori_workout_id',
        'durasi_menit',
        'tutorial_id', // <-- Tambahkan ini
    ];

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
    }
     public function kategoriWorkout(): BelongsTo
    {
        return $this->belongsTo(KategoriWorkout::class); // Sesuaikan dengan nama model kategori Anda
    }

    /**
     * TAMBAHKAN INI: Mendefinisikan bahwa sebuah Workout dimiliki oleh (belongs to) satu Tutorial.
     */
    public function tutorial(): BelongsTo
    {
        // Pastikan Anda memiliki model App\Models\Tutorial
        return $this->belongsTo(Tutorial::class);
    }

    
}