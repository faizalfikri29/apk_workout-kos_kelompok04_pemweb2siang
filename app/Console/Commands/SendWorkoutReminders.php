<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\WorkoutReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendWorkoutReminders extends Command
{
    protected $signature = 'app:send-workout-reminders';
    protected $description = 'Periksa dan kirim notifikasi pengingat latihan kepada pengguna.';

    public function handle()
    {
        $this->info('Memeriksa pengingat untuk dikirim...');

        // Ambil waktu saat ini dalam format HH:MM
        $currentTime = now()->format('H:i');

        // Cari semua pengguna yang waktu pengingatnya cocok dengan waktu saat ini
        $users = User::whereTime('reminder_time', $currentTime)->get();

        if ($users->isEmpty()) {
            $this->info('Tidak ada pengguna untuk diingatkan pada waktu: ' . $currentTime);
            return;
        }

        $this->info('Menemukan ' . $users->count() . ' pengguna. Mengirim notifikasi...');

        // Kirim notifikasi ke setiap pengguna yang ditemukan
        Notification::send($users, new WorkoutReminder());

        $this->info('Semua notifikasi berhasil dikirim.');
        return self::SUCCESS;
    }
}
