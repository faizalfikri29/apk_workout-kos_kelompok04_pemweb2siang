<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkoutSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'workout_id',
        'duration_seconds',
    ];

    /**
     * Mendefinisikan relasi ke model User.
     * Satu log latihan dimiliki oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi ke model Workout.
     * Satu log latihan merujuk ke satu jenis workout.
     */
    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }
    
}

