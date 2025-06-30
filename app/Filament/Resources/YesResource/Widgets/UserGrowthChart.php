<?php

// namespace App\Filament\Widgets;

// // 1. Ubah statement `use` untuk menunjuk ke kelas kustom Anda
// use App\Services\Trend\Trend;
// use App\Services\Trend\TrendValue;
// use App\Models\User;
// use Filament\Widgets\ChartWidget;

// class UserGrowthChart extends ChartWidget
// {
//     protected static ?string $heading = 'Pertumbuhan Pengguna (Custom Trend)';
    
//     protected static ?int $sort = 2;

//     protected function getData(): array
//     {
//         // 2. Kode ini sekarang memanggil kelas Trend kustom Anda
//         $data = Trend::model(User::class)
//             ->between(
//                 start: now()->subYear(),
//                 end: now(),
//             )
//             ->perMonth()
//             ->count(); // Ini akan mengembalikan Collection dari objek TrendValue

//         return [
//             'datasets' => [
//                 [
//                     'label' => 'Pengguna Baru',
//                     'data' => $data->map(fn (TrendValue $value) => $value->aggregate)->all(),
//                     'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
//                     'borderColor' => 'rgb(75, 192, 192)',
//                 ],
//             ],
//             'labels' => $data->map(fn (TrendValue $value) => $value->date)->all(),
//         ];
//     }

//     protected function getType(): string
//     {
//         return 'line';
//     }
// }
