<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkoutReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        // Kita akan mengirim notifikasi ini melalui email dan (nantinya) ke database untuk push notification
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Waktunya Olahraga! 💪')
                    ->greeting('Hai ' . $notifiable->name . ',')
                    ->line('Ini pengingat untuk jadwal latihanmu hari ini. Jangan sampai terlewat, ya!')
                    ->line('Luangkan 15 menit hari ini untuk tubuh yang lebih sehat.')
                    ->action('Mulai Latihan', url('/')) // Arahkan ke aplikasi
                    ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    // Ini akan menyimpan notifikasi ke database, berguna untuk push notification di aplikasi mobile
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Waktunya Olahraga! 💪',
            'body' => 'Jangan lupa jadwal latihanmu hari ini, ya!',
        ];
    }
}
