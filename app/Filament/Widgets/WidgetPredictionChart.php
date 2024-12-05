<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\EmployeePayment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class WidgetPredictionChart extends ChartWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Prediksi Keuntungan Bulan Depan';
    protected static string $color = 'success';

    protected function getData(): array
    {
        // Ambil data dua bulan terakhir
        $startDate = Carbon::now()->subMonths(2)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Hitung keuntungan bulanan
        $monthlyProfitData = $this->getMonthlyProfitData($startDate, $endDate);

        // Prediksi keuntungan bulan depan
        $predictedProfit = $this->predictNextMonthProfit($monthlyProfitData);

        // Format data untuk chart
        $nextMonthLabel = now()->addMonth()->format('F Y');
        $monthlyLabels = array_keys($monthlyProfitData);
        $monthlyValues = array_values($monthlyProfitData);

        // Pastikan semua label tersinkron dengan data
        $allLabels = [...$monthlyLabels, $nextMonthLabel];
        $profitValuesWithNull = array_merge($monthlyValues, [null]); // Tambahkan `null` di akhir untuk prediksi

        return [
            'datasets' => [
                [
                    'label' => 'Keuntungan Bulanan',
                    'data' => $profitValuesWithNull,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Prediksi Bulan Depan (' . $nextMonthLabel . ')',
                    'data' => array_merge(array_fill(0, count($monthlyLabels), null), [$predictedProfit]),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $allLabels,
        ];
    }

    protected function getMonthlyProfitData(Carbon $startDate, Carbon $endDate): array
    {
        // Ambil total pemasukan bulanan
        $incomes = $this->getMonthlyTotals(Transaction::incomes(), $startDate, $endDate);

        // Ambil total pengeluaran bulanan
        $expenses = $this->getMonthlyTotals(Transaction::expenses(), $startDate, $endDate);

        // Ambil total pembayaran karyawan bulanan
        $employeePayments = $this->getMonthlyTotals(EmployeePayment::query(), $startDate, $endDate, 'payment_date');

        // Hitung keuntungan bulanan hanya untuk bulan dengan data
        $months = collect(array_merge(array_keys($incomes), array_keys($expenses), array_keys($employeePayments)))
            ->unique()
            ->sort()
            ->take(3); // Ambil hanya dua bulan terakhir

        $profits = [];
        foreach ($months as $month) {
            $profits[$month] = ($incomes[$month] ?? 0) - ($expenses[$month] ?? 0) - ($employeePayments[$month] ?? 0);
        }

        return $profits;
    }

    protected function getMonthlyTotals($query, Carbon $startDate, Carbon $endDate, $dateColumn = 'date_transaction'): array
    {
        return $query
            ->whereBetween($dateColumn, [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT($dateColumn, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
    }

    protected function predictNextMonthProfit(array $profits): float
    {
        $profitValues = array_values($profits);

        // Jika data kurang dari 2 bulan, prediksi akan default ke rata-rata
        if (count($profitValues) < 2) {
            return array_sum($profitValues) / max(count($profitValues), 1);
        }

        $x = range(1, count($profitValues));
        $y = $profitValues;

        // Hitung regresi linier
        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = array_sum(array_map(fn ($xi, $yi) => $xi * $yi, $x, $y));
        $sumX2 = array_sum(array_map(fn ($xi) => $xi ** 2, $x));

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX ** 2);
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Prediksi untuk bulan berikutnya
        return $slope * ($n + 1) + $intercept;
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
