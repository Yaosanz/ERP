<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\EmployeePayment;
use App\Models\TransactionsExpense;
use App\Models\TransactionsIncomes;
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

    // Buat daftar lengkap label bulan termasuk bulan prediksi
    $allMonths = collect()
        ->merge(range(0, 2)) // Dua bulan terakhir + bulan prediksi
        ->map(fn ($i) => Carbon::now()->subMonths(2 - $i)->format('F Y'))
        ->toArray();

    // Tambahkan label untuk bulan depan (prediksi)
    $nextMonthLabel = now()->addMonth()->format('F Y');
    $allLabels = [...$allMonths, $nextMonthLabel];

    $monthlyProfitData = $this->getMonthlyProfitData($startDate, $endDate);

$monthlyValues = collect($allMonths)
    ->map(function ($month) use ($monthlyProfitData) {
        $formattedMonth = Carbon::createFromFormat('F Y', $month)->format('Y-m');
        return $monthlyProfitData[$formattedMonth] ?? 0;
    })
    ->toArray();



    // Tambahkan `null` untuk nilai prediksi pada dataset pertama
    $profitValuesWithNull = array_merge($monthlyValues, [null]);

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
                'data' => array_merge(array_fill(0, count($allMonths), null), [$this->predictNextMonthProfit($monthlyProfitData)]),
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
    $transactionIncomes = $this->getMonthlyTotals(Transaction::incomes(), $startDate, $endDate);
    $transactionsIncomes = $this->getMonthlyTotals(TransactionsIncomes::incomes(), $startDate, $endDate);
    $incomes = $this->mergeMonthlyData($transactionIncomes, $transactionsIncomes);

    // Ambil total pengeluaran bulanan
    $transactionExpenses = $this->getMonthlyTotals(Transaction::expenses(), $startDate, $endDate);
    $transactionsExpenses = $this->getMonthlyTotals(TransactionsExpense::expenses(), $startDate, $endDate);
    $expenses = $this->mergeMonthlyData($transactionExpenses, $transactionsExpenses);

    // Ambil total pembayaran karyawan bulanan
    $employeePayments = $this->getMonthlyTotals(EmployeePayment::query(), $startDate, $endDate, 'payment_date');

    // Hitung keuntungan bulanan
    $months = collect(array_merge(array_keys($incomes), array_keys($expenses), array_keys($employeePayments)))
        ->unique()
        ->sort()
        ->take(3);

    $profits = [];
    foreach ($months as $month) {
        $profits[$month] = 
            ($incomes[$month] ?? 0) - 
            ($expenses[$month] ?? 0) - 
            ($employeePayments[$month] ?? 0);
    }

    return $profits;
}



// Helper method to merge monthly data from different sources
protected function mergeMonthlyData(array $data1, array $data2): array
{
    $allKeys = array_unique(array_merge(array_keys($data1), array_keys($data2)));

    $mergedData = [];
    foreach ($allKeys as $month) {
        $mergedData[$month] = ($data1[$month] ?? 0) + ($data2[$month] ?? 0);
    }

    return $mergedData;
}



    protected function getMonthlyTotals($query, Carbon $startDate, Carbon $endDate, $dateColumn = 'date_transaction'): array
{
    $data = $query
        ->whereBetween($dateColumn, [$startDate, $endDate])
        ->selectRaw("DATE_FORMAT($dateColumn, '%Y-%m') as month, SUM(amount) as total")
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();


    return $data;
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
