<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\WorkoutLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkoutSessionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'duration_seconds' => 'required|integer|min:1',
        ]);

        try {
            WorkoutLog::create([
                'user_id' => Auth::id(),
                'jadwal_id' => $request->jadwal_id,
                'duration_seconds' => $request->duration_seconds,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan workout log: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan.'], 500);
        }

        return response()->json(['message' => 'Workout log berhasil disimpan.']);
    }

    public function start(Jadwal $jadwal)
    {
        $jadwal->load('workouts.tutorial');

        return view('workout_session', [
            'jadwal' => $jadwal
        ]);
    }
}
