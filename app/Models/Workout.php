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
        'durasi_menit',
        'tutorial_id', // <-- Tambahkan ini
    ];

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(Jadwal::class);
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