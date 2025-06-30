<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\WorkoutSession; // <-- INI PENTING DAN SUDAH DITAMBAHKAN

class Workout extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'description',
        'difficulty',
        'duration',
        'calories',
        'video_url',
        'image_path',
    ];

    /**
     * Mendefinisikan relasi: Satu Latihan bisa ada di banyak Sesi Latihan.
     */
    public function workoutSessions(): HasMany
    {
        return $this->hasMany(WorkoutSession::class);
    }
}
