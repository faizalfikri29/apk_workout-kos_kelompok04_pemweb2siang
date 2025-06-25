<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\WorkoutLog; // Import WorkoutLog
use Illuminate\Support\Facades\Auth; // Import Auth

class WorkoutController extends Controller
{
    // ... method startSession ...
    public function startSession(Jadwal $jadwal)
    {
        $jadwal->load('workouts.tutorial'); 
        return view('user.workout_session', ['jadwal' => $jadwal]);
    }

    // TAMBAHKAN METHOD BARU DI BAWAH INI
    public function logSession(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'duration_seconds' => 'required|integer|min:1',
        ]);

        // Estimasi kalori: 5 kalori per menit (bisa disesuaikan)
        $calories = round(($request->duration_seconds / 60) * 5);

        WorkoutLog::create([
            'user_id' => Auth::id(),
            'jadwal_id' => $request->jadwal_id,
            'duration_seconds' => $request->duration_seconds,
            'calories_burned' => $calories,
        ]);

        return response()->json(['success' => true, 'message' => 'Workout logged successfully.']);
    }
}