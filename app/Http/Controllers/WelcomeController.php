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
                'description' => 'bikin password baru, ga lama kemnudian lupa',
                'imageUrl' => 'https://i.pinimg.com/736x/dd/37/49/dd37497430a34ec48350c2b864645917.jpg'
            ],
            [
                'name' => 'Muhamad Faizal Fikri',
                'role' => 'Sistem Informasi',
                'description' => 'Tetap semangat walaupun pusing datang',
                'imageUrl' => 'https://i.pinimg.com/736x/a6/92/a1/a692a109971179007eabc4f607658d73.jpg'
            ],
            [
                'name' => 'Eka Ferdy Febriyansah',
                'role' => 'Sistem Informasi',
                'description' => 'Juru kunci database. Jangan sampai salah query, nanti data hilang dia yang dicari.',
                'imageUrl' => 'https://i.pinimg.com/1200x/22/99/fa/2299fa5ce714b9c57b20e243a50048f2.jpg'
            ],
            [
                'name' => 'Fariza Zea De Asminto',
                'role' => 'Sistem Informasi',
                'description' => 'Workout biar nggak gampang capek pas naik tangga ke lantai 3. Itu aja sih, terima gaji🙏',
                'imageUrl' => 'https://i.pinimg.com/736x/70/6c/62/706c62dd2eb78809f475901c6a20a85b.jpg'
            ],
            [
                'name' => 'Fathiyah Az Zahra Karim',
                'role' => 'Sistem Informasi',
                'description' => 'hidup itu asyik kalo ga mikirin deadline ',
                'imageUrl' => 'https://i.pinimg.com/736x/1b/11/ed/1b11edcc51b7ad4e5125727be0a645cd.jpg'
            ],
            [
                'name' => 'Khaila Nazwa',
                'role' => 'Sistem Informasi',
                'description' => 'mikirin deadline aja udah bikin pusing, apalagi mikirin kata-kata',
                'imageUrl' => 'https://i.pinimg.com/736x/1d/3b/35/1d3b350e3b954ec08ca321de631ffc78.jpg'
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