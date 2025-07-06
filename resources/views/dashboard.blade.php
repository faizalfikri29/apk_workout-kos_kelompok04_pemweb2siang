<x-app-layout>
    {{-- Slot Header dengan Sapaan Dinamis & Kutipan Motivasi --}}
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $greeting ?? 'Halo' }}, {{ Auth::user()->name }}!
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 italic">
                @php
                    $quotes = [
                        "Satu-satunya latihan yang buruk adalah yang tidak dilakukan.",
                        "Keringat hari ini adalah kekuatan hari esok.",
                        "Rasa sakit yang kamu rasakan hari ini adalah kekuatan yang kamu rasakan besok.",
                        "Proses tidak akan mengkhianati hasil.",
                    ];
                @endphp
                "{{ $quotes[array_rand($quotes)] }}"
            </p>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ newAchievement: {{ session()->has('newAchievement') ? 'true' : 'false' }} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Statistik Utama dengan Animasi Angka dan On-Load --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8" x-data x-init="
                $el.childNodes.forEach((node, i) => {
                    if (node.nodeType === 1) {
                        setTimeout(() => { node.classList.remove('opacity-0', 'translate-y-4') }, (i * 100));
                    }
                })
            ">
                {{-- Kartu Total Sesi --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg flex items-center space-x-4 transition-all duration-500 opacity-0 translate-y-4 hover:shadow-xl hover:-translate-y-1">
                    <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-xl"><x-heroicon-o-play-circle class="h-8 w-8 text-blue-500" /></div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Sesi</p>
                        <p class="text-3xl font-bold text-gray-800 dark:text-gray-100" x-data="counter({{ $stats['totalSesi'] ?? 0 }})" x-text="Math.round(current)"></p>
                    </div>
                </div>
                {{-- Kartu Total Menit --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg flex items-center space-x-4 transition-all duration-500 opacity-0 translate-y-4 hover:shadow-xl hover:-translate-y-1">
                    <div class="bg-green-100 dark:bg-green-900 p-3 rounded-xl"><x-heroicon-o-clock class="h-8 w-8 text-green-500" /></div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Menit</p>
                        <p class="text-3xl font-bold text-gray-800 dark:text-gray-100" x-data="counter({{ $stats['totalMenit'] ?? 0 }})" x-text="Math.round(current)"></p>
                    </div>
                </div>
                {{-- Kartu Workout Streak --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg flex items-center space-x-4 transition-all duration-500 opacity-0 translate-y-4 hover:shadow-xl hover:-translate-y-1">
                    <div class="bg-orange-100 dark:bg-orange-900 p-3 rounded-xl"><x-heroicon-o-fire class="h-8 w-8 text-orange-500" /></div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Workout Streak</p>
                        <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                            <span x-data="counter({{ $stats['streak'] ?? 0 }})" x-text="Math.round(current)"></span> <span class="text-base font-medium">Hari</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Kolom Kiri --}}
                <div class="lg:col-span-2 space-y-8" x-data x-init="
                    $el.childNodes.forEach((node, i) => {
                        if (node.nodeType === 1) {
                            setTimeout(() => { node.classList.remove('opacity-0', 'translate-y-4') }, 200 + (i * 100));
                        }
                    })
                ">
                    {{-- Latihan Hari Ini --}}
                    @if($latihanHarian)
                        <div class="relative bg-gradient-to-br from-indigo-700 to-purple-800 overflow-hidden shadow-2xl rounded-2xl text-white transition-all duration-500 opacity-0 translate-y-4">
                            <div class="p-8">
                                <h3 class="text-2xl font-bold">Latihan Hari Ini</h3>
                                <div class="mt-4 p-5 border-2 border-dashed border-white/50 rounded-lg bg-white/10">
                                    <h4 class="text-xl font-semibold">{{ $latihanHarian->nama_jadwal }}</h4>
                                    <p class="mt-1 text-sm text-indigo-200">{{ $latihanHarian->deskripsi }}</p>
                                    <div class="mt-4">
                                        <span class="text-xs font-semibold bg-white/20 text-white px-2 py-1 rounded-full">{{ $latihanHarian->workouts_count }} jenis latihan</span>
                                        <span class="text-xs font-semibold bg-white/20 text-white px-2 py-1 rounded-full ml-2">Estimasi: {{ $latihanHarian->workouts_count * 3 }} menit</span>
                                    </div>
                                </div>
                                <a href="{{ route('workout.session.start', $latihanHarian->id) }}" class="mt-6 inline-flex items-center justify-center w-full px-6 py-4 bg-white border border-transparent rounded-xl font-bold text-indigo-700 uppercase tracking-widest hover:bg-indigo-100 transition text-lg shadow-lg animate-pulse hover:animate-none">
                                    Mulai Latihan Sekarang!
                                    <x-heroicon-o-arrow-right class="w-6 h-6 ml-3" />
                                </a>
                            </div>
                        </div>
                    @else
                        {{-- Kartu Hari Istirahat --}}
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl flex items-center p-8 transition-all duration-500 opacity-0 translate-y-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Hari Istirahat!</h3>
                                <p class="mt-2 text-gray-600 dark:text-gray-300">Tubuhmu butuh waktu untuk pulih. Manfaatkan hari ini untuk istirahat.</p>
                                <a href="{{ route('user.workouts.index') }}" class="mt-4 inline-flex items-center text-indigo-600 dark:text-indigo-400 font-semibold">
                                    Lihat Semua Jadwal Latihan <x-heroicon-o-arrow-right class="w-4 h-4 ml-1" />
                                </a>
                            </div>
                            <x-heroicon-o-sparkles class="h-24 w-24 text-yellow-400 ml-8 opacity-50 hidden sm:block" />
                        </div>
                    @endif
                    

                    
                    {{-- Notifikasi --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl rounded-3xl transition-all duration-500 opacity-0 translate-y-4">
                        <div class="p-8 text-gray-900 dark:text-gray-100">
                            <h4 class="text-3xl font-extrabold mb-8 flex items-center text-indigo-700 dark:text-indigo-400">
                                <x-heroicon-o-sparkles class="h-9 w-9 mr-4 text-yellow-400 animate-pulse" />
                                Pesan Penting Untukmu
                            </h4>
                            @if($notifications->isNotEmpty())
                                <ul class="space-y-5">
                                    @foreach($notifications as $notification)
                                        <li class="group flex items-start p-7 bg-gradient-to-br from-white to-indigo-50 dark:from-gray-900 dark:to-gray-800 rounded-2xl shadow-xl border border-transparent hover:border-indigo-400 dark:hover:border-blue-600 transform transition-all duration-300 hover:scale-[1.015] hover:shadow-2xl cursor-pointer relative overflow-hidden">
                                            <div class="absolute inset-0 bg-indigo-100 dark:bg-blue-900 opacity-0 group-hover:opacity-10 transition-opacity duration-300 pointer-events-none rounded-2xl"></div>
                                            <div class="flex-shrink-0 mr-5">
                                                <x-heroicon-o-megaphone class="h-10 w-10 text-indigo-500 dark:text-blue-400 group-hover:rotate-6 transition-transform duration-300" />
                                            </div>
                                            <div class="flex-grow">
                                                <h5 class="font-bold text-2xl text-gray-900 dark:text-gray-100 mb-1 leading-tight">{{ $notification->title }}</h5>
                                                <div class="text-base text-gray-700 dark:text-gray-300 mt-2 leading-relaxed">
                                                    {!! $notification->message !!}
                                                </div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 flex items-center">
                                                    <x-heroicon-o-clock class="h-4 w-4 mr-1" />
                                                    Diterima <span class="font-semibold ml-1">{{ $notification->created_at->diffForHumans() }}</span>
                                                </p>
                                            </div>
                                            <div class="ml-auto pl-6 flex-shrink-0 self-center">
                                                <x-heroicon-o-arrow-right-circle class="h-8 w-8 text-indigo-400 dark:text-blue-500 opacity-70 group-hover:opacity-100 group-hover:translate-x-1 transition-all duration-300" />
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center py-12 bg-gray-100 dark:bg-gray-700 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                                    <x-heroicon-o-document-magnifying-glass class="h-24 w-24 text-gray-400 mx-auto mb-6 opacity-75" />
                                    <p class="text-xl font-semibold text-gray-600 dark:text-gray-300">Belum ada pesan untuk Anda.</p>
                                    <p class="text-base text-gray-500 dark:text-gray-400 mt-3">Tetaplah berlatih dan cek secara berkala!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan --}}
                <div class="lg:col-span-1 space-y-8" x-data x-init="
                     $el.childNodes.forEach((node, i) => {
                         if (node.nodeType === 1) {
                             setTimeout(() => { node.classList.remove('opacity-0', 'translate-y-4') }, 400 + (i * 100));
                         }
                     })
                ">
                    {{-- Kalender --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl transition-all duration-500 opacity-0 translate-y-4" x-data="workoutCalendar({ workoutDates: {{ json_encode($workoutDates ?? []) }} })" x-cloak>
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                             <div class="flex justify-between items-center">
                                 <h3 class="text-lg font-semibold" x-text="`${monthNames[month]} ${year}`"></h3>
                                 <div>
                                     <button @click="prevMonth()" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">&lt;</button>
                                     <button @click="nextMonth()" class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">&gt;</button>
                                 </div>
                             </div>
                             <div class="mt-4 grid grid-cols-7 gap-2 text-center text-xs">
                                 <template x-for="day in days" :key="day"><div class="text-gray-500 font-medium" x-text="day"></div></template>
                                 <template x-for="blankday in blankdays"><div></div></template>
                                 <template x-for="date in no_of_days" :key="date">
                                     <div class="w-full h-8 flex items-center justify-center rounded-full"
                                           :class="{
                                             'bg-green-500 text-white font-bold': isWorkoutDay(date),
                                             'bg-indigo-600 text-white': isToday(date) && isWorkoutDay(date),
                                             'ring-2 ring-indigo-500': isToday(date) && !isWorkoutDay(date)
                                           }"
                                           x-text="date">
                                     </div>
                                 </template>
                             </div>
                        </div>
                    </div>
                    
                    {{-- Latihan Favorit --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg transition-all duration-500 opacity-0 translate-y-4">
                        <h4 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Akses Cepat Favorit</h4>
                        @if(isset($favoriteWorkouts) && $favoriteWorkouts->isNotEmpty())
                            <ul class="space-y-3">
                                @foreach($favoriteWorkouts as $fav)
                                <li>
                                    <a href="{{ $fav->route }}" class="group flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors">
                                        <x-heroicon-o-heart class="h-6 w-6 text-indigo-500 mr-4"/>
                                        <span class="flex-1 font-medium text-gray-700 dark:text-gray-300">{{ $fav->name }}</span>
                                        <x-heroicon-o-chevron-right class="h-5 w-5 text-gray-400 group-hover:text-indigo-600 transition-colors"/>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-500">Belum ada latihan favorit.</p>
                                <p class="text-xs text-gray-400 mt-1">Selesaikan latihan untuk menambahkannya.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Menuju Lencana Berikutnya --}}
                    @if(isset($nextAchievement))
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg transition-all duration-500 opacity-0 translate-y-4">
                        <h4 class="text-lg font-semibold mb-3 text-gray-900 dark:text-gray-100">Menuju Lencana Berikutnya</h4>
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <x-dynamic-component :component="'heroicon-s-' . $nextAchievement->icon" class="h-12 w-12 text-gray-400 dark:text-gray-500" />
                            </div>
                            <div class="w-full">
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $nextAchievement->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $nextAchievement->description }}</p>
                                <div class="mt-2">
                                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                        <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2.5 rounded-full" style="width: {{ $nextAchievementProgress ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Lencana Penghargaan --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl transition-all duration-500 opacity-0 translate-y-4">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h4 class="text-lg font-semibold mb-4">Lencana Penghargaan</h4>
                            @if(isset($allAchievements) && $allAchievements->isNotEmpty())
                                <div class="grid grid-cols-4 gap-4">
                                    @foreach($allAchievements as $achievement)
                                        @php $unlocked = isset($userAchievementIds) && in_array($achievement->id, $userAchievementIds); @endphp
                                        <div class="text-center" title="{{ $achievement->name }} - {{ $achievement->description }}">
                                            <div class="relative">
                                                <x-dynamic-component :component="'heroicon-s-' . $achievement->icon" class="h-12 w-12 mx-auto transition-all {{ $unlocked ? 'text-yellow-400 hover:scale-110' : 'text-gray-300 dark:text-gray-600 opacity-60' }}" />
                                                @if(!$unlocked)
                                                    <x-heroicon-s-lock-closed class="h-5 w-5 absolute bottom-0 right-2 text-gray-400 dark:text-gray-500" />
                                                @endif
                                            </div>
                                            <p class="text-xs mt-2 truncate font-semibold {{ $unlocked ? '' : 'text-gray-400 dark:text-gray-500' }}">{{ $achievement->name }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">Lencana akan muncul di sini. Teruslah berlatih!</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Notifikasi Lencana Baru --}}
        @if(session()->has('newAchievement'))
        <div x-show="newAchievement" x-transition @click.away="newAchievement = false" class="fixed inset-0 bg-black/75 flex items-center justify-center p-4 z-50" x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl text-center p-8 max-w-sm mx-auto" @click.stop>
                <h3 class="text-sm font-semibold uppercase text-yellow-500 tracking-wider">Lencana Baru Terbuka!</h3>
                <div class="my-4">
                    <x-dynamic-component :component="'heroicon-s-' . session('newAchievement')->icon" class="h-24 w-24 mx-auto text-yellow-400 animate-pulse" />
                </div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ session('newAchievement')->name }}</h2>
                <p class="text-gray-600 dark:text-gray-300 mt-2">{{ session('newAchievement')->description }}</p>
                <button @click="newAchievement = false" class="mt-6 w-full px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-500 transition">Luar Biasa!</button>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>

{{-- Pastikan Chart.js diimpor di layout utama Anda --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('alpine:init', () => {
    // Komponen untuk animasi counter angka
    Alpine.data('counter', (target) => ({
        current: 0,
        target: parseFloat(target) || 0,
        init() {
            let frame = () => {
                const increment = this.target / 100;
                if (this.current < this.target) {
                    this.current += increment;
                    requestAnimationFrame(frame);
                } else {
                    this.current = this.target;
                }
            };
            setTimeout(() => requestAnimationFrame(frame), 250);
        }
    }));

    // Komponen untuk kalender
    Alpine.data('workoutCalendar', (options) => ({
        month: '', year: '', no_of_days: [], blankdays: [],
        days: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
        workoutDates: options.workoutDates || [],
        init() {
            const today = new Date();
            this.month = today.getMonth();
            this.year = today.getFullYear();
            this.getNoOfDays();
        },
        isToday(date) {
            const today = new Date();
            const d = new Date(this.year, this.month, date);
            return today.toDateString() === d.toDateString();
        },
        isWorkoutDay(date) {
            const d = new Date(this.year, this.month, date);
            const formattedDate = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
            return this.workoutDates.includes(formattedDate);
        },
        getNoOfDays() {
            let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
            let dayOfWeek = new Date(this.year, this.month).getDay();
            this.blankdays = Array.from({ length: dayOfWeek });
            this.no_of_days = Array.from({ length: daysInMonth }, (_, i) => i + 1);
        },
        prevMonth() {
            if (this.month == 0) { this.year--; this.month = 11; } else { this.month--; }
            this.getNoOfDays();
        },
        nextMonth() {
            if (this.month == 11) { this.month = 0; this.year++; } else { this.month++; }
            this.getNoOfDays();
        },
    }));
});

// Inisialisasi semua grafik setelah DOM siap
document.addEventListener('DOMContentLoaded', () => {
    // Grafik Progress Mingguan
    const weeklyChartEl = document.getElementById('weeklyProgressChart');
    if (weeklyChartEl) {
        new Chart(weeklyChartEl, {
            type: 'line',
            data: {
                labels: {!! json_encode(isset($stats['weekly_progress_labels']) ? $stats['weekly_progress_labels'] : []) !!},
                datasets: [{
                    label: 'Menit Latihan',
                    data: {!! json_encode(isset($stats['weekly_progress_data']) ? $stats['weekly_progress_data'] : []) !!},
                    backgroundColor: 'rgba(79, 70, 229, 0.2)', borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 2, tension: 0.4, fill: true, pointBackgroundColor: 'rgba(79, 70, 229, 1)', pointBorderColor: '#fff',
                }]
            },
            options: { scales: { y: { beginAtZero: true } }, responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }

    // Grafik Distribusi Tipe Latihan
    const workoutTypeChartEl = document.getElementById('workoutTypeChart');
    if (workoutTypeChartEl) {
        new Chart(workoutTypeChartEl, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(isset($stats['workout_type_distribution']['labels']) ? $stats['workout_type_distribution']['labels'] : []) !!},
                datasets: [{
                    data: {!! json_encode(isset($stats['workout_type_distribution']['data']) ? $stats['workout_type_distribution']['data'] : []) !!},
                    backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(251, 191, 36, 0.8)'],
                    borderColor: '#fff', borderWidth: 2,
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { padding: 20 } } } }
        });
    }
});
</script>
