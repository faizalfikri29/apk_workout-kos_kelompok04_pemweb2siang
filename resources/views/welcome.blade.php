<!DOCTYPE html>
<html lang="id" class="scroll-smooth" 
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" 
      x-init="darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? (localStorage.getItem('darkMode') !== 'false') : (localStorage.getItem('darkMode') === 'true');
              $watch('darkMode', val => localStorage.setItem('darkMode', val)); 
              $watch('darkMode', val => document.documentElement.classList.toggle('dark', val))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Workout-in-Kos - Transformasi Kebugaran Mahasiswa</title>
    
    <!-- Preload fonts for better performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"></noscript>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js and Chart.js (with defer for better performance) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js" defer></script>
    
    <script>
        // Tailwind CSS configuration
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s ease-in-out infinite',
                        'bounce-slow': 'bounce 2s infinite',
                        'wiggle': 'wiggle 1s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        wiggle: {
                            '0%, 100%': { transform: 'rotate(-3deg)' },
                            '50%': { transform: 'rotate(3deg)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom CSS */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .dark .gradient-text {
            background: linear-gradient(135deg, #a78bfa 0%, #c084fc 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .dark .glass {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        #loading {
            transition: opacity 0.3s ease-out; /* Added for fade out effect */
        }
        /* x-cloak for Alpine.js */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-inter antialiased bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 text-slate-700 dark:text-slate-300 transition-all duration-500">

<div id="loading" class="fixed inset-0 bg-white dark:bg-slate-900 z-50 flex items-center justify-center">
    <div class="text-center">
        <div class="w-16 h-16 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-lg font-semibold gradient-text">Memuat Workout-in-Kos...</p>
    </div>
</div>

<div class="fixed bottom-6 right-6 z-40" x-data="{ showFAB: false }" x-show="showFAB" @scroll.window="showFAB = window.pageYOffset > 300" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4">
    <button @click="window.scrollTo({top: 0, behavior: 'smooth'})" 
            class="bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-full shadow-2xl transition-all duration-300 transform hover:scale-110 animate-bounce-slow"
            aria-label="Kembali ke atas halaman">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>
</div>

<div x-data="{ 
        isModalOpen: false, 
        modalTutorial: { name: '', description: '', videoUrl: '', duration: '', difficulty: '' } 
      }" 
      x-show="isModalOpen" 
      @keydown.escape.window="isModalOpen = false"
      class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4"
      x-trap.inert.noscroll="isModalOpen"
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-cloak> 
    
    <div @click.away="isModalOpen = false" 
          class="bg-white dark:bg-slate-800 rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto shadow-2xl relative"
          x-show="isModalOpen"
          x-transition:enter="transition ease-out duration-300"
          x-transition:enter-start="opacity-0 scale-90"
          x-transition:enter-end="opacity-100 scale-100">
        
        <div class="aspect-video bg-slate-900 rounded-t-2xl overflow-hidden">
            <iframe :src="modalTutorial.videoUrl"
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                    allowfullscreen
                    class="w-full h-full"
                    title="Video Tutorial Workout">
            </iframe>
        </div>
        <div class="p-8">
            <div class="flex justify-between items-start mb-4 flex-wrap gap-4">
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white" x-text="modalTutorial.name"></h3>
                <div class="flex gap-2">
                    <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-full text-sm font-medium" x-text="modalTutorial.duration"></span>
                    <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-sm font-medium" x-text="modalTutorial.difficulty"></span>
                </div>
            </div>
            <p class="text-slate-600 dark:text-slate-400 leading-relaxed" x-text="modalTutorial.description"></p>
            <div class="mt-6 flex flex-col sm:flex-row gap-4">
                <button @click="alert('Ditambahkan ke favorit!')" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-semibold transition-all duration-300">
                    ❤️ Tambah ke Favorit
                </button>
                <button @click="alert('Dibagikan!')" class="px-6 py-3 border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl font-semibold transition-all duration-300">
                    📤 Bagikan
                </button>
            </div>
        </div>
        <button @click="isModalOpen = false" class="absolute top-4 right-4 text-white/70 hover:text-white bg-black/30 hover:bg-black/50 p-2 rounded-full transition-all duration-300" aria-label="Tutup modal">
             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
             </svg>
        </button>
    </div>
</div>

<header x-data="{ 
            atTop: true, 
            mobileMenu: false,
            notifications: false 
          }" 
          @scroll.window="atTop = (window.pageYOffset < 50)"
          class="sticky top-0 z-40 w-full transition-all duration-500"
          :class="{ 'glass shadow-xl': !atTop, 'bg-transparent': atTop }">
    <div class="container mx-auto px-6">
        <nav class="flex justify-between items-center py-4">
            <a href="#" class="text-3xl font-black gradient-text flex items-center gap-2" aria-label="Beranda Workout-Kos">
                💪 Workout-Kos
            </a>
            
            <div class="hidden lg:flex items-center space-x-8">
                <a href="#fitur" class="font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Fitur</a>
                <a href="#tutorials" class="font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Tutorial</a>
                <a href="#tools" class="font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Tools</a>
                <a href="#jadwal" class="font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Jadwal</a>
                <a href="#anggota" class="font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Anggota</a>
                <a href="#nutrition" class="font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Nutrisi</a>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button @click="notifications = !notifications" class="p-2 rounded-full hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors relative" aria-label="Notifikasi">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM11 4.702a.5.5 0 01.832-.374l1.99 1.658a.5.5 0 01.158.374V9a3 3 0 106 0V6.734a.5.5 0 01.158-.374l1.99-1.658A.5.5 0 0123 5.298V17a1 1 0 01-1 1h-4l-5-5v5a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1h8.702z"></path>
                        </svg>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                    </button>
                    <div x-show="notifications" @click.away="notifications = false" 
                          x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0 scale-95"
                          x-transition:enter-end="opacity-100 scale-100"
                          class="absolute right-0 mt-2 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 py-2"
                          x-cloak>
                        <div class="px-4 py-2 border-b border-slate-200 dark:border-slate-700">
                            <h3 class="font-semibold">Notifikasi</h3>
                        </div>
                        <div class="p-2 space-y-1">
                            <div class="p-3 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer">
                                <p class="text-sm font-medium">🎯 Reminder: Workout pagi hari ini!</p>
                                <p class="text-xs text-slate-500 mt-1">2 menit yang lalu</p>
                            </div>
                            <div class="p-3 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer">
                                <p class="text-sm font-medium">📊 Progress mingguan sudah siap!</p>
                                <p class="text-xs text-slate-500 mt-1">1 jam yang lalu</p>
                            </div>
                            <div class="p-3 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg cursor-pointer">
                                <p class="text-sm font-medium">🏆 Tantangan baru tersedia!</p>
                                <p class="text-xs text-slate-500 mt-1">3 jam yang lalu</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button @click="darkMode = !darkMode" class="p-2 rounded-full hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
                        :aria-label="darkMode ? 'Alihkan ke mode terang' : 'Alihkan ke mode gelap'">
                    <svg x-show="!darkMode" class="w-6 h-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <svg x-show="darkMode" class="w-6 h-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>
                
                <div class="hidden lg:flex items-center gap-4">
                    <a href="login" class="font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        Login
                    </a>
                    <a href="register" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2.5 rounded-full font-semibold hover:shadow-lg hover:shadow-indigo-400/50 transition-all duration-300 transform hover:scale-105">
                        Register
                    </a>
                </div>
                <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-full hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors" aria-label="Toggle menu mobile">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </nav>
        
        <div x-show="mobileMenu" x-transition class="lg:hidden py-4 space-y-2" x-cloak>
            <a href="#fitur" @click="mobileMenu = false" class="block py-2 font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Fitur</a>
            <a href="#tutorials" @click="mobileMenu = false" class="block py-2 font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Tutorial</a>
            <a href="#tools" @click="mobileMenu = false" class="block py-2 font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Tools</a>
            <a href="#jadwal" @click="mobileMenu = false" class="block py-2 font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Jadwal</a>
            <a href="#anggota" @click="mobileMenu = false" class="block py-2 font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Anggota</a>
            <a href="#nutrition" @click="mobileMenu = false" class="block py-2 font-semibold hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Nutrisi</a>
            <div class="border-t border-slate-200 dark:border-slate-700 mt-4 pt-4 flex items-center gap-4">
                <a href="Login" class="flex-1 text-center font-semibold py-3 rounded-lg border-2 border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    Login
                </a>
                <a href="Register" class="flex-1 text-center bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 rounded-lg hover:shadow-lg hover:shadow-indigo-400/50 transition-all duration-300">
                    Register
                </a>
            </div>
            </div>
    </div>
</header>

<main>
    <section class="relative pt-20 pb-32 overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-indigo-400/30 to-purple-400/30 rounded-full filter blur-3xl animate-float"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-gradient-to-tr from-pink-400/30 to-indigo-400/30 rounded-full filter blur-3xl animate-float" style="animation-delay: 2s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-gradient-to-r from-yellow-400/20 to-orange-400/20 rounded-full filter blur-3xl animate-pulse-slow"></div>
        </div>

        <div class="container mx-auto px-6 text-center relative z-10">
            <div class="max-w-5xl mx-auto">
                <div class="inline-flex items-center gap-2 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-200 px-4 py-2 rounded-full text-sm font-semibold mb-8 animate-bounce-slow">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    🎉 Gratis untuk Mahasiswa Indonesia!
                </div>
                
                <h1 class="text-6xl md:text-8xl font-black text-slate-900 dark:text-white leading-tight mb-8">
                    Workout di <span class="gradient-text">Kamar Kost</span> Jadi Mudah!
                </h1>
                
                <p class="text-xl md:text-2xl text-slate-600 dark:text-slate-400 max-w-3xl mx-auto mb-12 leading-relaxed">
                    Platform workout terlengkap untuk mahasiswa Indonesia. Latihan efektif tanpa gym, tanpa alat mahal, langsung dari kamar kost mu! 💪
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                    <a href="login">
                    <button class="group bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold px-8 py-4 rounded-full hover:shadow-2xl hover:shadow-indigo-400/50 transition-all duration-300 transform hover:scale-105 flex items-center gap-3">
                        🚀 Mulai Workout Sekarang
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    </a>
                    <button class="flex items-center gap-3 text-slate-700 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 font-semibold transition-colors">
                        ▶️ Tonton Demo (2 menit)
                    </button>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="text-4xl font-black gradient-text mb-2">{{ $activeUsersCount ?? '1000' }}+</div>
                        <div class="text-slate-600 dark:text-slate-400 font-semibold">Mahasiswa Aktif</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-black gradient-text mb-2">{{ $tutorialsCount ?? '200' }}+</div>
                        <div class="text-slate-600 dark:text-slate-400 font-semibold">Video Tutorial</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-black gradient-text mb-2">{{ $universitiesCount ?? '50' }}+</div>
                        <div class="text-slate-600 dark:text-slate-400 font-semibold">Universitas</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-black gradient-text mb-2">{{ $userRating ?? '4.9' }}⭐</div>
                        <div class="text-slate-600 dark:text-slate-400 font-semibold">Rating Pengguna</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-6 -mt-20 relative z-10 mb-24">
        <ul class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <li class="glass rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 cursor-pointer">
                <div class="text-4xl mb-4">🏠</div>
                <h3 class="text-xl font-bold mb-2 text-slate-900 dark:text-white">Workout di Kamar</h3>
                <p class="text-slate-600 dark:text-slate-400">Latihan efektif dalam ruang terbatas 2x2 meter</p>
            </li>
            <li class="glass rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 cursor-pointer">
                <div class="text-4xl mb-4">🎯</div>
                <h3 class="text-xl font-bold mb-2 text-slate-900 dark:text-white">Tanpa Alat Mahal</h3>
                <p class="text-slate-600 dark:text-slate-400">Maksimalkan barang-barang yang ada di kost</p>
            </li>
            <li class="glass rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 cursor-pointer">
                <div class="text-4xl mb-4">📱</div>
                <h3 class="text-xl font-bold mb-2 text-slate-900 dark:text-white">Smart Reminder</h3>
                <p class="text-slate-600 dark:text-slate-400">Notifikasi pintar sesuai jadwal kuliah</p>
            </li>
        </ul>
    </section>

    <section id="jadwal" class="py-24 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-slate-800/50 dark:to-indigo-900/20">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-5xl font-black text-slate-900 dark:text-white mb-4">📅 Rencanakan Workout-mu</h2>
                <p class="text-xl text-slate-600 dark:text-slate-400">Buat jadwal latihan yang sesuai dengan kuliah dan kegiatanmu!</p>
            </div>
            
            <div x-data="{ 
                    workoutDays: [],
                    workoutTime: '',
                    selectedWorkouts: []
                  }" class="max-w-4xl mx-auto glass rounded-2xl p-8 shadow-2xl">
                <div class="space-y-6">
                    <div>
                        <label for="workout-days" class="block text-lg font-semibold mb-2">Pilih Hari Latihan</label>
                        <div id="workout-days" class="flex flex-wrap gap-3">
                            <template x-for="day in ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']" :key="day">
                                <button @click="workoutDays.includes(day) ? workoutDays.splice(workoutDays.indexOf(day), 1) : workoutDays.push(day)" 
                                        :class="{'bg-indigo-600 text-white': workoutDays.includes(day), 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300': !workoutDays.includes(day)}"
                                        class="px-4 py-2 rounded-full font-semibold transition-all duration-300">
                                    <span x-text="day"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label for="workout-time" class="block text-lg font-semibold mb-2">Pilih Waktu Latihan</label>
                        <select id="workout-time" x-model="workoutTime" class="w-full p-3 rounded-lg bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                            <option value="">Pilih waktu</option>
                            <option value="Pagi (06:00-08:00)">Pagi (06:00-08:00)</option>
                            <option value="Siang (12:00-14:00)">Siang (12:00-14:00)</option>
                            <option value="Sore (16:00-18:00)">Sore (16:00-18:00)</option>
                            <option value="Malam (19:00-21:00)">Malam (19:00-21:00)</option>
                        </select>
                    </div>
                    <div>
                        <label for="workout-type" class="block text-lg font-semibold mb-2">Pilih Jenis Latihan</label>
                        <div id="workout-type" class="flex flex-wrap gap-3">
                            <template x-for="workout in ['Kardio', 'Kekuatan', 'Fleksibilitas', 'HIIT']" :key="workout">
                                <button @click="selectedWorkouts.includes(workout) ? selectedWorkouts.splice(selectedWorkouts.indexOf(workout), 1) : selectedWorkouts.push(workout)" 
                                        :class="{'bg-indigo-600 text-white': selectedWorkouts.includes(workout), 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300': !selectedWorkouts.includes(workout)}"
                                        class="px-4 py-2 rounded-full font-semibold transition-all duration-300">
                                    <span x-text="workout"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <button @click="alert('Jadwal disimpan!')" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-4 rounded-xl hover:shadow-xl hover:shadow-indigo-400/50 transition-all duration-300 transform hover:scale-105">
                        Simpan Jadwal
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section id="progress" class="py-24">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-5xl font-black text-slate-900 dark:text-white mb-4">📈 Lacak Progresmu</h2>
                <p class="text-xl text-slate-600 dark:text-slate-400">Pantau perkembangan workoutmu dengan grafik interaktif</p>
            </div>
            
            <div class="max-w-4xl mx-auto glass rounded-2xl p-8 shadow-2xl">
                <canvas id="progressChart" class="w-full h-64"></canvas>
            </div>
        </div>
    </section>

    <section id="tools" class="py-24 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-slate-800/50 dark:to-indigo-900/20">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-5xl font-black text-slate-900 dark:text-white mb-4">🛠️ Panduan Alat Minimal</h2>
                <p class="text-xl text-slate-600 dark:text-slate-400">Gunakan barang sehari-hari untuk workout efektif</p>
            </div>
            
            <ul class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <li class="glass rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 cursor-pointer">
                    <div class="text-4xl mb-4">💧</div>
                    <h3 class="text-xl font-bold mb-2 text-slate-900 dark:text-white">Botol Air</h3>
                    <p class="text-slate-600 dark:text-slate-400">Gunakan sebagai dumbbell untuk latihan kekuatan</p>
                </li>
                <li class="glass rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 cursor-pointer">
                    <div class="text-4xl mb-4">🎒</div>
                    <h3 class="text-xl font-bold mb-2 text-slate-900 dark:text-white">Tas Ransel</h3>
                    <p class="text-slate-600 dark:text-slate-400">Isi dengan buku untuk beban tambahan</p>
                </li>
                <li class="glass rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 cursor-pointer">
                    <div class="text-4xl mb-4">🪑</div>
                    <h3 class="text-xl font-bold mb-2 text-slate-900 dark:text-white">Kursi</h3>
                    <p class="text-slate-600 dark:text-slate-400">Untuk dips dan step-up di kamar kost</p>
                </li>
            </ul>
        </div>
    </section>

    <section id="quiz" class="py-24">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-5xl font-black text-slate-900 dark:text-white mb-4">🤔 Temukan Workout Idealmu</h2>
                <p class="text-xl text-slate-600 dark:text-slate-400">Ikuti kuis singkat untuk rekomendasi latihan yang sesuai</p>
            </div>
            
            <div x-data="{ 
                    step: 1,
                    time: '',
                    energy: '',
                    space: '',
                    result: ''
                  }" class="max-w-4xl mx-auto glass rounded-2xl p-8 shadow-2xl">
                <div x-show="step === 1" x-transition>
                    <h3 class="text-2xl font-bold mb-4">Berapa banyak waktu yang kamu miliki?</h3>
                    <div class="flex flex-wrap gap-3">
                        <button @click="time = '5-10 menit'; step = 2" class="flex-1 p-4 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-all">5-10 menit</button>
                        <button @click="time = '15-20 menit'; step = 2" class="flex-1 p-4 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-all">15-20 menit</button>
                        <button @click="time = '30+ menit'; step = 2" class="flex-1 p-4 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-all">30+ menit</button>
                    </div>
                </div>
                <div x-show="step === 2" x-transition>
                    <h3 class="text-2xl font-bold mb-4">Seberapa banyak energi yang kamu miliki?</h3>
                    <div class="flex flex-wrap gap-3">
                        <button @click="energy = 'Rendah'; step = 3" class="flex-1 p-4 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-all">Rendah</button>
                        <button @click="energy = 'Sedang'; step = 3" class="flex-1 p-4 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-all">Sedang</button>
                        <button @click="energy = 'Tinggi'; step = 3" class="flex-1 p-4 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-all">Tinggi</button>
                    </div>
                </div>
                <div x-show="step === 3" x-transition>
                    <h3 class="text-2xl font-bold mb-4">Berapa luas ruang yang tersedia?</h3>
                    <div class="flex flex-wrap gap-3">
                        <button @click="space = 'Sangat terbatas'; step = 4" class="flex-1 p-4 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-all">Sangat terbatas</button>
                        <button @click="space = 'Sedang'; step = 4" class="flex-1 p-4 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-all">Sedang</button>
                        <button @click="space = 'Luas'; step = 4" class="flex-1 p-4 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-all">Luas</button>
                    </div>
                </div>
                <div x-show="step === 4" x-transition>
                    <h3 class="text-2xl font-bold mb-4">Rekomendasi Workout</h3>
                    <p class="text-lg mb-6" x-text="`Berdasarkan waktu ${time}, energi ${energy}, dan ruang ${space}, kami merekomendasikan: ${time === '5-10 menit' ? 'Latihan HIIT cepat' : time === '15-20 menit' ? 'Circuit training' : 'Full-body workout'}`"></p>
                    <button @click="step = 1; time = ''; energy = ''; space = ''; result = ''" class="w-full p-4 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-all">Ulangi Kuis</button>
                </div>
            </div>
        </div>
    </section>

    <section id="tutorials" class="py-24 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-slate-800/50 dark:to-indigo-900/20" x-data="{ filter: 'all' }">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-5xl font-black text-slate-900 dark:text-white mb-4">🎥 Tutorial Workout</h2>
                <p class="text-xl text-slate-600 dark:text-slate-400">Pilih tutorial yang sesuai dengan kebutuhanmu</p>
            </div>
            
            <div class="mb-8 flex flex-wrap gap-4 justify-center">
                <button @click="filter = 'all'" :class="{'bg-indigo-600 text-white': filter === 'all', 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300': filter !== 'all'}" class="px-4 py-2 rounded-full font-semibold transition-all">Semua</button>
                <button @click="filter = 'Beginner'" :class="{'bg-indigo-600 text-white': filter === 'Beginner', 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300': filter !== 'Beginner'}" class="px-4 py-2 rounded-full font-semibold transition-all">Pemula</button>
                <button @click="filter = 'Intermediate'" :class="{'bg-indigo-600 text-white': filter === 'Intermediate', 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300': filter !== 'Intermediate'}" class="px-4 py-2 rounded-full font-semibold transition-all">Menengah</button>
                <button @click="filter = 'Advanced'" :class="{'bg-indigo-600 text-white': filter === 'Advanced', 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300': filter !== 'Advanced'}" class="px-4 py-2 rounded-full font-semibold transition-all">Lanjutan</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tutorials as $tutorial)
                <div x-show="filter === 'all' || filter === '{{ $tutorial->kategoriWorkout->name ?? 'Semua Level' }}'" 
                     class="glass rounded-2xl p-6 hover:scale-105 transition-all duration-300 cursor-pointer" 
                     @click="isModalOpen = true; modalTutorial = {
                         name: '{{ $tutorial->nama_tutorial }}',
                         description: '{{ $tutorial->deskripsi_tutorial }}',
                         videoUrl: '{{ $tutorial->url_video }}',
                         duration: '15 Menit',
                         difficulty: '{{ $tutorial->kategoriWorkout->name ?? 'Semua Level' }}'
                     }">
                    <img src="{{ Storage::url($tutorial->gambar_url) }}" 
                         alt="{{ $tutorial->nama_tutorial }}" class="w-full h-48 object-cover rounded-xl mb-4" loading="lazy">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $tutorial->nama_tutorial }}</h3>
                    <p class="text-slate-600 dark:text-slate-400">{{ Str::limit($tutorial->deskripsi_tutorial, 40) }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="anggota" class="py-24">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-black text-slate-900 dark:text-white mb-4">👥 Tim Pengembang Kami</h2>
                <p class="text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">Orang-orang hebat di balik layar yang mendedikasikan waktu dan keahliannya untuk Workout-in-Kos.</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-12">
                @foreach($developers as $developer)
                <div class="text-center group">
                    <div class="relative w-40 h-40 mx-auto mb-6">
                        <img src="{{ $developer['imageUrl'] }}" 
                             alt="Foto {{ $developer['name'] }}" 
                             class="w-full h-full rounded-full object-cover shadow-lg transform group-hover:scale-110 transition-transform duration-300" loading="lazy">
                        <div class="absolute inset-0 rounded-full border-4 border-indigo-500/50 group-hover:border-indigo-500 transition-all duration-300 scale-110 group-hover:scale-125 opacity-0 group-hover:opacity-100"></div>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-1">{{ $developer['name'] }}</h3>
                    <p class="text-indigo-600 dark:text-indigo-400 font-semibold mb-3">{{ $developer['role'] }}</p>
                    <p class="text-slate-600 dark:text-slate-400 max-w-xs mx-auto">{{ $developer['description'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="nutrition" class="py-24 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-slate-800/50 dark:to-indigo-900/20">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-5xl font-black text-slate-900 dark:text-white mb-4">🍎 Tips Nutrisi Mahasiswa</h2>
                <p class="text-xl text-slate-600 dark:text-slate-400">Makanan sehat dan murah untuk mendukung workout</p>
            </div>
            
            <ul class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($nutritions as $nutrition)
                <li class="glass rounded-2xl p-6 text-center hover:scale-105 transition-all duration-300 cursor-pointer">
                    <div class="text-4xl mb-4">{{ $nutrition['icon'] }}</div>
                    <h3 class="text-xl font-bold mb-2 text-slate-900 dark:text-white">{{ $nutrition['title'] }}</h3>
                    <p class="text-slate-600 dark:text-slate-400">{{ $nutrition['description'] }}</p>
                </li>
                @endforeach
            </ul>
        </div>
    </section>

    @if($workoutOfTheDay)
    <section id="fitur" class="py-24">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-5xl font-black text-slate-900 dark:text-white mb-4">🔥 Workout of the Day</h2>
                <p class="text-xl text-slate-600 dark:text-slate-400">Tantangan harian yang disesuaikan dengan mahasiswa Indonesia</p>
            </div>
            
            <div class="max-w-6xl mx-auto bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-slate-800/50 dark:to-indigo-900/20 rounded-3xl p-8 lg:p-12 shadow-2xl">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="relative">
                        <img src="{{ Storage::url($workoutOfTheDay->gambar_url) }}" 
                             alt="{{ $workoutOfTheDay->nama_tutorial }}" class="w-full h-80 object-cover rounded-2xl shadow-xl" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent rounded-2xl"></div>
                        <div class="absolute bottom-4 left-4 text-white">
                            <div class="flex gap-2 mb-2">
                                <span class="bg-green-500 px-3 py-1 rounded-full text-sm font-semibold">{{ $workoutOfTheDay->kategoriWorkout->name ?? 'Semua Level' }}</span>
                                <span class="bg-blue-500 px-3 py-1 rounded-full text-sm font-semibold">15 Menit</span>
                            </div>
                        </div>
                        <button class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white/20 backdrop-blur-sm text-white p-6 rounded-full hover:bg-white/30 transition-all duration-300 animate-pulse" aria-label="Play Workout of the Day">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div>
                        <h3 class="text-4xl font-black text-slate-900 dark:text-white mb-4">{{ $workoutOfTheDay->nama_tutorial }}</h3>
                        <p class="text-lg text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">
                            {{ $workoutOfTheDay->deskripsi_tutorial }}
                        </p>
                        
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">1</span>
                                </div>
                                <span class="font-semibold">Pemanasan dinamis (3 menit)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">2</span>
                                </div>
                                <span class="font-semibold">Circuit training ringan (10 menit)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">3</span>
                                </div>
                                <span class="font-semibold">Cool down & stretching (2 menit)</span>
                            </li>
                        </ul>
                        
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button onclick="alert('Workout dimulai! 💪')" 
                                    class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-4 rounded-xl hover:shadow-xl hover:shadow-indigo-400/50 transition-all duration-300 transform hover:scale-105">
                                🚀 Mulai Sekarang
                            </button>
                            <button onclick="alert('Disimpan ke jadwal!')" 
                                    class="px-6 py-4 border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl font-semibold transition-all duration-300">
                                📅 Simpan ke Jadwal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
</main>

<script>
    // Logika untuk menyembunyikan loading screen dan inisialisasi Chart.js
    document.addEventListener('DOMContentLoaded', () => {
        // Sembunyikan loading screen setelah DOMContentLoaded
        const loadingScreen = document.getElementById('loading');
        if (loadingScreen) {
            loadingScreen.style.opacity = 0; // Transisi opacity untuk efek fade out
            setTimeout(() => {
                loadingScreen.style.display = 'none';
            }, 300); // Sesuaikan durasi transisi agar sesuai dengan fade out
        }

        // Inisialisasi Chart.js
        const ctx = document.getElementById('progressChart');
        if (ctx) {
            const isDarkMode = document.documentElement.classList.contains('dark');
            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                    datasets: [{
                        label: 'Jumlah Latihan',
                        data: [3, 5, 7, 10], // Data ini bisa diisi dari database
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Jumlah Sesi' },
                            grid: {
                                color: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                color: isDarkMode ? '#cbd5e1' : '#475569'
                            }
                        },
                        x: {
                            title: { display: true, text: 'Minggu' },
                            grid: {
                                color: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                color: isDarkMode ? '#cbd5e1' : '#475569'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: isDarkMode ? '#cbd5e1' : '#475569'
                            }
                        }
                    }
                }
            });
        }
    });
</script>

</body>
</html>
