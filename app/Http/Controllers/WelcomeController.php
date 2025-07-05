<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tutorial;
use App\Models\Workout;
use App\Models\KategoriWorkout;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WelcomeController extends Controller
{
    public function index()
    {
        // Mengambil data statistik
        $activeUsersCount = User::where('role', 'user')->count();
        $tutorialsCount = Tutorial::count();
        $workoutOfTheDay = Tutorial::with('kategoriWorkout')->inRandomOrder()->first();

        // Data dummy untuk universitas dan rating
        $universitiesCount = 50;
        $userRating = 4.9;

        // Mengambil semua tutorial
        $tutorials = Tutorial::with('kategoriWorkout')->get();
        
        // Mengambil data nutrisi (contoh data statis)
        $nutritions = [
            ['icon' => '🥚', 'title' => 'Telur Rebus', 'description' => 'Sumber protein murah, mudah disiapkan'],
            ['icon' => '🥗', 'title' => 'Sayur Kol', 'description' => 'Kombinasi sayur segar dengan saus sederhana'],
            ['icon' => '🍠', 'title' => 'Ubi Panggang', 'description' => 'Karbohidrat sehat untuk energi tahan lama'],
        ];

        // Data untuk tim pengembang
        $developers = [
            [
                'name' => 'Hanung Tri Atmojo',
                'role' => 'Sistem Informasi',
                'description' => 'Pawangnya if-else, semua masalah diselesaikan dengan percabangan.',
                'imageUrl' => 'https://placehold.co/400x400/667eea/ffffff?text=HTA'
            ],
            [
                'name' => 'Muhamad Faizal Fikri',
                'role' => 'Sistem Informasi',
                'description' => 'Tukang hias website. Kalau ada yang miring-miring, dia yang lurusin pake CSS.',
                'imageUrl' => 'https://placehold.co/400x400/764ba2/ffffff?text=MFF'
            ],
            [
                'name' => 'Eka Ferdy Febriyansah',
                'role' => 'Sistem Informasi',
                'description' => 'Juru kunci database. Jangan sampai salah query, nanti data hilang dia yang dicari.',
                'imageUrl' => 'https://placehold.co/400x400/a78bfa/ffffff?text=EFF'
            ],
            [
                'name' => 'Fariza Zea De Asminto',
                'role' => 'Sistem Informasi',
                'description' => 'Ahli pewarnaan tombol dan pembuat icon lucu. Katanya, UX adalah segalanya.',
                'imageUrl' => 'https://placehold.co/400x400/c084fc/ffffff?text=FZA'
            ],
            [
                'name' => 'Fathiyah Az Zahra Karim',
                'role' => 'Sistem Informasi',
                'description' => 'Penjaga gerbang server. Kalau web lemot, dia yang pertama kali kena omel.',
                'imageUrl' => 'https://placehold.co/400x400/3b82f6/ffffff?text=FAZ'
            ],
            [
                'name' => 'Khaila Nazwa',
                'role' => 'Sistem Informasi',
                'description' => 'Pencari bug profesional. Mottonya: \'Kalau tidak rusak, jangan diperbaiki.\'',
                'imageUrl' => 'https://placehold.co/400x400/10b981/ffffff?text=KN'
            ],
        ];

        return view('welcome', compact(
            'activeUsersCount',
            'tutorialsCount',
            'universitiesCount',
            'userRating',
            'tutorials',
            'workoutOfTheDay',
            'nutritions',
            'developers' // Menambahkan data developer ke view
        ));
    }
}
