<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Hi, {{ Auth::user()->name }}. Siap untuk berkeringat?
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Latihan Hari Ini</h3>
                            @if($latihanHarian)
                                <div class="mt-4 p-4 border dark:border-gray-700 rounded-lg">
                                    <h4 class="text-xl font-semibold">{{ $latihanHarian->nama_jadwal }}</h4>
                                    <p class="mt-1 text-sm dark:text-gray-300">{{ $latihanHarian->deskripsi }}</p>
                                    <div class="mt-4">
                                        <span class="text-sm bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 px-2 py-1 rounded-full">{{ $latihanHarian->workouts_count }} jenis latihan</span>
                                        <span class="text-sm bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 px-2 py-1 rounded-full ml-2">Estimasi: {{ $latihanHarian->workouts_count * 3 }} menit</span>
                                    </div>
                                </div>
                                <a href="{{ route('workout.session.start', $latihanHarian->id) }}" class="mt-6 inline-block w-full text-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-600 transition text-lg">
                                    Mulai Latihan Sekarang!
                                </a>
                            @else
                                <p class="mt-4 text-gray-500">Tidak ada rekomendasi latihan hari ini. Coba cek lagi besok!</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" 
                         x-data="workoutCalendar({ workoutDates: {{ json_encode($workoutDates) }} })">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold" x-text="`${monthNames[month]} ${year}`"></h3>
                                <div>
                                    <button @click="prevMonth()" class="px-2 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700">&lt;</button>
                                    <button @click="nextMonth()" class="px-2 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-700">&gt;</button>
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-7 gap-1 text-center text-xs">
                                <template x-for="day in days" :key="day">
                                    <div class="text-gray-500" x-text="day"></div>
                                </template>
                                <template x-for="blankday in blankdays">
                                    <div></div>
                                </template>
                                <template x-for="date in no_of_days" :key="date">
                                    <div class="w-full h-10 flex items-center justify-center rounded" 
                                         :class="{
                                            'bg-green-500 text-white': isWorkoutDay(date),
                                            'bg-indigo-500 text-white': isToday(date) && isWorkoutDay(date),
                                            'ring-2 ring-indigo-500': isToday(date) && !isWorkoutDay(date)
                                         }"
                                         x-text="date">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-center">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                           <h4 class="text-lg font-semibold">Progress Saya</h4>
                           <div class="mt-4 flex justify-around">
                               <div>
                                   <p class="text-3xl font-bold">{{ $stats['totalSesi'] }}</p>
                                   <p class="text-xs text-gray-500">Sesi</p>
                               </div>
                               <div>
                                   <p class="text-3xl font-bold">{{ $stats['totalMenit'] }}</p>
                                   <p class="text-xs text-gray-500">Menit</p>
                               </div>
                               <div>
                                   <p class="text-3xl font-bold text-orange-400">{{ $stats['streak'] }}</p>
                                   <p class="text-xs text-gray-500">Streak</p>
                               </div>
                           </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                           <h4 class="text-lg font-semibold mb-4">Lencana Penghargaan</h4>
                           @if($userAchievements->isNotEmpty())
                                <div class="grid grid-cols-4 gap-4">
                                    @foreach($userAchievements as $achievement)
                                        <div class="text-center" x-data="{ tooltip: false }">
                                            @svg($achievement->icon, 'h-10 w-10 mx-auto text-yellow-400')
                                            <p class="text-xs mt-1 truncate">{{ $achievement->name }}</p>
                                        </div>
                                    @endforeach
                                </div>
                           @else
                                <p class="text-sm text-gray-500">Teruslah berlatih untuk mendapatkan lencana pertamamu!</p>
                           @endif
                        </div>
                    </div>

                    @if($latihanMingguan)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h4 class="text-lg font-semibold">Tantangan Lain</h4>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $latihanMingguan->nama_jadwal }}</p>
                            <a href="{{ route('workout.session.start', $latihanMingguan->id) }}" class="mt-4 inline-block w-full text-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 transition">
                                Coba Latihan
                            </a>
                        </div>
                    </div>
                     @endif
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function workoutCalendar(options) {
        return {
            month: '',
            year: '',
            no_of_days: [],
            blankdays: [],
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
                
                this.blankdays = Array.from({ length: dayOfWeek }, (_, i) => i + 1);
                this.no_of_days = Array.from({ length: daysInMonth }, (_, i) => i + 1);
            },

            prevMonth() {
                if (this.month == 0) {
                    this.year--;
                    this.month = 11;
                } else {
                    this.month--;
                }
                this.getNoOfDays();
            },

            nextMonth() {
                if (this.month == 11) {
                    this.month = 0;
                    this.year++;
                } else {
                    this.month++;
                }
                this.getNoOfDays();
            },
        }
    }
    </script>
</x-app-layout>