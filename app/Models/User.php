<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\WorkoutLog; // Ini sudah benar
use App\Models\Achievement; // Tambahkan untuk relasi achievements

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all of the workoutLogs for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workoutLogs(): HasMany // NAMA FUNGSI DIPERBAIKI
    {
        // MODEL YANG DITUJU JUGA DIPERBAIKI
        return $this->hasMany(WorkoutLog::class); 
    }

    /**
     * The achievements that belong to the User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function achievements(): BelongsToMany
    {
        // Pastikan nama tabel pivot sudah benar (contoh: 'achievement_user')
        return $this->belongsToMany(Achievement::class, 'user_achievements');
    }
}
