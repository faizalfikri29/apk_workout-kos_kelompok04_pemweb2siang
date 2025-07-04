<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriWorkout extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kategori_workouts';


    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all of the workouts for the KategoriWorkout
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class, 'kategori_workout_id');
    }
}
