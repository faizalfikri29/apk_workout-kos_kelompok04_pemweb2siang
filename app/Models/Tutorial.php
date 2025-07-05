<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tutorial extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_tutorial',
        'deskripsi_tutorial',
        'url_video',
        'kategori_workout_id',
        'gambar_url',
        'deksripsi_tutorial',
        // 'name',
        'kategori_workout_id',
    ];

    /**
     * Get the kategoriWorkout that owns the Tutorial.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kategoriWorkout(): BelongsTo
    {
        return $this->belongsTo(KategoriWorkout::class, 'kategori_workout_id');
    }
}
