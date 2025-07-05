<?php

// File: app/Http/Controllers/WorkoutSessionController.php

namespace App\Http\Controllers;

use App\Models\Jadwal; // Diperbaiki: Menggunakan model Jadwal yang benar
use App\Models\WorkoutLog; // Diperbaiki: Menggunakan model WorkoutLog untuk menyimpan data
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Ditambahkan untuk logging jika terjadi error

class WorkoutSessionController extends Controller
{
    /**
     * Menyimpan semua workout dari jadwal yang telah selesai ke dalam workout_logs.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'jadwal_id' => 'required|exists:jadwals,id',
            'workouts' => 'required|array', // Pastikan form mengirim array ID workout
            'workouts.*' => 'exists:workouts,id',
            'duration_seconds_per_workout' => 'required|integer|min:1' // Durasi per workout dalam detik
        ]);

        $user = Auth::user();
        $workoutIds = $request->input('workouts');
        $durationPerWorkout = $request->input('duration_seconds_per_workout');

        try {
            // Loop melalui setiap workout yang diselesaikan dan buat log untuk masing-masing
            foreach ($workoutIds as $workoutId) {
                WorkoutLog::create([
                    'user_id' => $user->id,
                    'workout_id' => $workoutId,
                    'duration_seconds' => $durationPerWorkout,
                ]);
            }
        } catch (\Exception $e) {
            // Jika terjadi error, catat di log dan kembalikan dengan pesan error
            Log::error('Gagal menyimpan workout log: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan progres Anda.');
        }

        // Redirect kembali ke dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Kerja bagus! Latihan berhasil dicatat.');
    }

    /**
     * Menampilkan halaman sesi latihan.
     * Anda perlu mengirim data workout dari jadwal yang dipilih ke view ini.
     */
    public function start(Jadwal $jadwal)
    {
        // Memuat semua workout yang ada di dalam jadwal ini
        $jadwal->load('workouts');

        return view('workout_session', [
            'jadwal' => $jadwal,
            'workouts' => $jadwal->workouts
        ]);
    }
}
