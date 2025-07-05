<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sesi Latihan - {{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-900 text-gray-200">

    @if($jadwal->workouts->isNotEmpty())

    @php
        $exercises = $jadwal->workouts->map(function($w) {
            return [
                'id' => $w->id,
                'name' => $w->nama_workout,
                'duration' => $w->durasi_menit * 60,
                'tutorial' => $w->tutorial->gambar_url ?? ''
            ];
        })->toJson();
    @endphp

    <div 
        class="container mx-auto p-4 md:p-8"
        x-data='workoutSession({
            jadwalId: {{ $jadwal->id }},
            exercises: {!! $exercises !!},
            restDuration: 30
        })'
        x-cloak
    >
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold">{{ $jadwal->nama_workout }}</h1>
            <p class="text-lg text-gray-400" x-text="statusText"></p>
        </div>

        <div class="bg-gray-800 shadow-xl rounded-lg overflow-hidden max-w-2xl mx-auto">
            <div class="p-6 md:p-10 text-center">
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-4" x-text="currentStepName"></h2>
                <div class="text-8xl md:text-9xl font-mono font-bold text-indigo-400 my-8" x-text="formatTime(timer)"></div>
                
                <template x-if="!isResting && currentExercise.tutorial">
                    <img :src="'/storage/' + currentExercise.tutorial" alt="Tutorial" class="max-w-xs mx-auto rounded-lg mb-4">
                </template>

                <div class="flex justify-center space-x-4">
                    <button @click="togglePause()" class="px-8 py-3 rounded-full text-white font-bold text-lg" :class="isPaused ? 'bg-green-600 hover:bg-green-500' : 'bg-yellow-600 hover:bg-yellow-500'">
                        <span x-text="isPaused ? 'Lanjut' : 'Jeda'"></span>
                    </button>
                    <button @click="nextStep()" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-500 rounded-full text-white font-bold text-lg">
                        Lewati
                    </button>
                </div>
            </div>
            <div class="w-full bg-gray-700">
                <div class="bg-indigo-500 text-xs leading-none py-1 text-center text-white" :style="`width: ${progressPercentage}%`" x-text="`${Math.round(progressPercentage)}%`"></div>
            </div>
        </div>
        
        <template x-if="isFinished">
            <div class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center">
                <div class="bg-gray-800 p-8 rounded-lg text-center shadow-xl">
                    <h2 class="text-3xl font-bold text-green-400">Kerja Bagus!</h2>
                    <p class="mt-2">Anda telah menyelesaikan sesi latihan.</p>
                    <a href="{{ route('dashboard') }}" class="mt-6 inline-block px-6 py-2 bg-indigo-600 text-white rounded-lg">Kembali ke Dashboard</a>
                </div>
            </div>
        </template>
    </div>

    @else
    <div class="container mx-auto p-4 md:p-8 text-center">
        <div class="bg-gray-800 shadow-xl rounded-lg p-10 max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-yellow-400 mb-4">Latihan Tidak Ditemukan</h1>
            <p class="text-lg text-gray-300">
                Jadwal latihan <strong>"{{ $jadwal->nama_jadwal }}"</strong> tidak memiliki daftar gerakan.
            </p>
            <a href="{{ route('dashboard') }}" class="mt-8 inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
    @endif

    <script>
        function workoutSession(options) {
            return {
                jadwalId: options.jadwalId,
                exercises: options.exercises || [],
                restDuration: options.restDuration || 30,
                totalTimeSpent: 0,
                currentIndex: 0,
                timer: 0,
                isResting: false,
                isPaused: true,
                isFinished: false,
                interval: null,

                init() { this.setStep(); },

                get currentExercise() {
                    return this.exercises[this.currentIndex];
                },
                get currentStepName() {
                    return this.isFinished ? 'Selesai!' : (this.isResting ? 'Istirahat' : this.currentExercise.name);
                },
                get statusText() {
                    if (this.isFinished) return 'Latihan telah selesai.';
                    const next = this.exercises[this.currentIndex + 1]?.name || 'Selesai';
                    return this.isResting ? `Berikutnya: ${next}` : `Latihan ${this.currentIndex + 1} dari ${this.exercises.length}`;
                },
                get progressPercentage() {
                    const steps = this.exercises.length * 2 - 1;
                    const done = this.isResting ? (this.currentIndex * 2) + 1 : this.currentIndex * 2;
                    return (done / steps) * 100;
                },

                setStep() {
                    if (this.currentIndex >= this.exercises.length) return this.finishWorkout();
                    this.isResting = false;
                    this.timer = this.currentExercise.duration;
                },
                startTimer() {
                    if (this.timer <= 0) return;
                    this.isPaused = false;
                    this.interval = setInterval(() => {
                        this.timer--;
                        this.totalTimeSpent++;
                        if (this.timer <= 0) this.nextStep();
                    }, 1000);
                },
                pauseTimer() {
                    this.isPaused = true;
                    clearInterval(this.interval);
                },
                togglePause() {
                    this.isPaused ? this.startTimer() : this.pauseTimer();
                },
                nextStep() {
                    clearInterval(this.interval);
                    this.isPaused = true;
                    if (!this.isResting) {
                        if (this.currentIndex < this.exercises.length - 1) {
                            this.isResting = true;
                            this.timer = this.restDuration;
                        } else {
                            this.finishWorkout();
                        }
                    } else {
                        this.currentIndex++;
                        this.setStep();
                    }
                },
                finishWorkout() {
                    this.isFinished = true;
                    clearInterval(this.interval);
                    fetch('{{ route('workout.log.store') }}', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
        jadwal_id: this.jadwalId,
        duration_seconds: this.totalTimeSpent
    })
})
.then(response => {
    if (!response.ok) throw new Error('Network response was not ok');
    return response.json();
})
.then(data => {
    console.log('Log saved:', data.message);
})
.catch(error => {
    console.error('Error logging workout:', error);
});

                },
                formatTime(seconds) {
                    const m = Math.floor(seconds / 60);
                    const s = seconds % 60;
                    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                }
            }
        }
    </script>
</body>
</html>
