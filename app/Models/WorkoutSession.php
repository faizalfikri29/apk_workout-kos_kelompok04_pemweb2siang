<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Kolom-kolom ini yang boleh diisi saat membuat data baru.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'workout_id',
        'duration_completed',
    ];

    /**
     * Mendefinisikan relasi: Setiap Sesi Latihan dimiliki oleh SATU Pengguna.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi: Setiap Sesi Latihan merujuk pada SATU jenis Latihan.
     */
    public function workout(): BelongsTo
    {
        return $this->belongsTo(Workout::class);
    }
}
