<?php

namespace App\Http\Controllers;

use App\Models\WorkoutLog;
use App\Models\Jadwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkoutLogController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'duration_seconds' => 'required|integer|min:1',
        ]);

        $jadwal = Jadwal::with('workouts')->findOrFail($request->jadwal_id);

        foreach ($jadwal->workouts as $workout) {
            WorkoutLog::create([
                'user_id' => Auth::id(),
                'workout_id' => $workout->id,
                'duration_seconds' => round($request->duration_seconds / count($jadwal->workouts)),
                'jadwal_id' => $jadwal->id, // Menyimpan ID jadwal
            ]);
        }

        return response()->json(['message' => 'Workout log berhasil disimpan.']);
    }
}