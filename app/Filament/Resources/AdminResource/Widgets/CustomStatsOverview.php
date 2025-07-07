<?php

namespace App\Filament\Widgets;

use App\Models\Jadwal;
use App\Models\User;
use App\Models\Workout;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CustomStatsOverview extends BaseWidget
{
    /**
     * @var int Waktu cache dalam detik (15 detik, sesuai polling).
     */
    protected int $cacheSeconds = 15;

    /**
     * @var int Jumlah hari untuk analisis tren.
     */
    protected int $trendDays = 7;

    /**
     * Me-refresh data setiap 15 detik.
     */
    protected static ?string $pollingInterval = '15s';

    /**
     * Memuat widget secara lazy untuk performa lebih baik.
     */
    protected static bool $isLazy = true;

    /**
     * Mendapatkan dan menyusun semua statistik.
     * Metode ini sekarang bertindak sebagai "orchestrator" yang memanggil metode lain.
     *
     * @return array
     */
    protected function getStats(): array
    {
        return [
            $this->getWelcomeStat(),
            $this->getUsersStat(),
            $this->getWorkoutsStat(),
            $this->getSchedulesStat(),
        ];
    }

    /**
     * Membuat kartu sapaan dinamis untuk pengguna yang login.
     *
     * @return Stat
     */
    private function getWelcomeStat(): Stat
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $hour = now()->hour;

        $greeting = match (true) {
            $hour < 12 => 'Selamat Pagi',
            $hour < 18 => 'Selamat Siang',
            default => 'Selamat Malam',
        };

        $welcomeMessage = sprintf('%s, %s (%s)!', $greeting, $user->name, ucfirst($user->role));

        return Stat::make('Panel Admin Workout Kos', $welcomeMessage)
            ->description('Data diperbarui: ' . now()->toTimeString())
            ->descriptionIcon('heroicon-m-clock')
            ->color('primary');
    }

    /**
     * Membuat kartu statistik untuk total pengguna dengan tren dan persentase perubahan.
     *
     * @return Stat
     */
    private function getUsersStat(): Stat
    {
        // Menggunakan cache untuk data pengguna agar tidak query berulang kali dalam interval singkat.
        $usersData = Cache::remember('stats_users', $this->cacheSeconds, function () {
            $currentPeriodCount = User::query()
                ->where('created_at', '>=', now()->subDays($this->trendDays))
                ->count();

            $previousPeriodCount = User::query()
                ->whereBetween('created_at', [
                    now()->subDays($this->trendDays * 2),
                    now()->subDays($this->trendDays)
                ])->count();
            
            // Query untuk data chart
            $trend = User::query()
                ->where('created_at', '>=', now()->subDays($this->trendDays - 1))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->pluck('count', 'date');

            return [
                'total' => User::count(),
                'current_period_count' => $currentPeriodCount,
                'previous_period_count' => $previousPeriodCount,
                'trend' => $trend,
            ];
        });

        // Hitung persentase perubahan
        $percentageChange = 0;
        if ($usersData['previous_period_count'] > 0) {
            $percentageChange = (($usersData['current_period_count'] - $usersData['previous_period_count']) / $usersData['previous_period_count']) * 100;
        } elseif ($usersData['current_period_count'] > 0) {
            $percentageChange = 100; // Jika sebelumnya 0 dan sekarang ada, anggap naik 100%
        }

        // Tentukan deskripsi dan ikon berdasarkan perubahan
        $descriptionIcon = $percentageChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        $color = $percentageChange >= 0 ? 'success' : 'danger';
        $description = sprintf(
            '%d pengguna baru dalam %d hari terakhir (%s%.2f%%)',
            $usersData['current_period_count'],
            $this->trendDays,
            $percentageChange >= 0 ? '+' : '',
            $percentageChange
        );

        // Menyiapkan data untuk grafik (lebih sederhana)
        $chartData = collect(range($this->trendDays - 1, 0, -1))
            ->mapWithKeys(fn ($day) => [now()->subDays($day)->toDateString() => 0])
            ->merge($usersData['trend'])
            ->values()
            ->toArray();
            
        return Stat::make('Total Pengguna', $usersData['total'])
            ->description($description)
            ->descriptionIcon($descriptionIcon)
            ->chart($chartData)
            ->color($color);
    }

    /**
     * Membuat kartu statistik untuk total jenis latihan.
     *
     * @return Stat
     */
    private function getWorkoutsStat(): Stat
    {
        // Data ini jarang berubah, cache bisa lebih lama jika diinginkan.
        $totalWorkouts = Cache::remember('stats_total_workouts', $this->cacheSeconds, function () {
            return Workout::count();
        });

        return Stat::make('Total Jenis Latihan', $totalWorkouts)
            ->description('Variasi latihan yang tersedia')
            ->descriptionIcon('heroicon-m-bolt')
            ->color('info');
    }

    /**
     * Membuat kartu statistik untuk total jadwal.
     *
     * @return Stat
     */
    private function getSchedulesStat(): Stat
    {
        $totalSchedules = Cache::remember('stats_total_schedules', $this->cacheSeconds, function () {
            return Jadwal::count();
        });

        return Stat::make('Total Jadwal', $totalSchedules)
            ->description('Jumlah jadwal latihan aktif')
            ->descriptionIcon('heroicon-m-calendar-days')
            ->color('warning');
    }
}
