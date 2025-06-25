

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Workout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($workouts as $workout)
                            <div class="bg-gray-100 p-4 rounded-lg">
                                <h3 class="font-bold text-lg">{{ $workout->nama }}</h3>
                                <p class="text-sm text-gray-600">Kategori: {{ $workout->kategoriWorkout->nama_kategori ?? 'Tidak ada kategori' }}</p>
                                <p class="mt-2">{{ $workout->deskripsi }}</p>
                                <div class="mt-4">
                                    <span class="inline-block bg-blue-200 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded-full">
                                        Durasi: {{ $workout->durasi }} menit
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p>Belum ada data workout yang tersedia.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>