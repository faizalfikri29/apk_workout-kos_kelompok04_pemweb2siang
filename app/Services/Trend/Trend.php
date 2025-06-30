<?php
// namespace App\Services\Trend;

// use App\Services\Trend\TrendValue;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Collection;
// use Illuminate\Support\Facades\DB;

// class Trend
// {
//     public Builder $query;
//     public string $dateColumn = 'created_at';
//     public Carbon $start;
//     public Carbon $end;
//     protected string $interval;
//     protected string $sqlDateFormat;

//     public static function model(string $modelClass): self
//     {
//         $trend = new self();
//         $trend->query = $modelClass::query();
//         return $trend;
//     }

//     public function between(Carbon $start, Carbon $end): self
//     {
//         $this->start = $start;
//         $this->end = $end;
//         return $this;
//     }

//     public function perMonth(): self
//     {
//         $this->interval = 'month';
//         $this->sqlDateFormat = '%Y-%m';
//         return $this;
//     }

//     public function perDay(): self
//     {
//         $this->interval = 'day';
//         $this->sqlDateFormat = '%Y-%m-%d';
//         return $this;
//     }

//     public function count(): Collection
//     {
//         $databaseDriver = $this->query->getConnection()->getDriverName();

//         $dateFunction = match ($databaseDriver) {
//             'mysql' => "DATE_FORMAT({$this->dateColumn}, '{$this->sqlDateFormat}')",
//             'sqlite' => "strftime('{$this->sqlDateFormat}', {$this->dateColumn})",
//             'pgsql' => "to_char({$this->dateColumn}, 'YYYY-MM-DD')",
//             default => "DATE_FORMAT({$this->dateColumn}, '{$this->sqlDateFormat}')"
//         };

//         $results = $this->query
//             ->whereBetween($this->dateColumn, [$this->start, $this->end])
//             ->select(
//                 DB::raw("{$dateFunction} as date_point"),
//                 DB::raw('count(*) as aggregate')
//             )
//             ->groupBy('date_point')
//             ->orderBy('date_point', 'asc')
//             ->get();

//         return $results->map(function ($result) {
//             return new TrendValue($result->date_point, $result->aggregate);
//         });
//     }
// }
