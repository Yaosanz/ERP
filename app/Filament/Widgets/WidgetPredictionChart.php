<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\EmployeePayment;
use App\Models\TransactionPayments;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class WidgetPredictionChart extends ChartWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Prediksi Keuntungan Bulan Depan';
    protected static string $color = 'success';

    protected function getData(): array
    {
        // Ambil data 4 bulan terakhir
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Buat daftar lengkap label bulan termasuk bulan prediksi
        $allMonths = collect()
            ->merge(range(0, 3)) // Empat bulan terakhir + bulan prediksi
            ->map(fn ($i) => Carbon::now()->subMonths(3 - $i)->format('F Y'))
            ->toArray();

        // Tambahkan label untuk bulan depan (prediksi)
        $nextMonthLabel = now()->addMonth()->format('F Y');
        $allLabels = [...$allMonths, $nextMonthLabel];

        $monthlyProfitData = $this->getMonthlyProfitData($startDate, $endDate);
        // $monthlyExpensesData = $this->getMonthlyExpensesData($startDate, $endDate);

        $monthlyValues = collect($allMonths)
            ->map(function ($month) use ($monthlyProfitData) {
                $formattedMonth = Carbon::createFromFormat('F Y', $month)->format('Y-m');
                return $monthlyProfitData[$formattedMonth] ?? 0;
            })
            ->toArray();

        // $monthlyExpensesValues = collect($allMonths)
        //     ->map(function ($month) use ($monthlyExpensesData) {
        //         $formattedMonth = Carbon::createFromFormat('F Y', $month)->format('Y-m');
        //         return $monthlyExpensesData[$formattedMonth] ?? 0;
        //     })
        //     ->toArray();

        // Tambahkan `null` untuk nilai prediksi pada dataset pertama
        $profitValuesWithNull = array_merge($monthlyValues, [null]);
        // $expensesValuesWithNull = array_merge($monthlyExpensesValues, [null]);

        return [
            'datasets' => [
                [
                    'label' => 'Keuntungan Bulanan',
                    'data' => $profitValuesWithNull,
                    'backgroundColor' => 'rgba(76, 175, 80, 0.2)',
                    'borderColor' => 'rgba(76, 175, 80, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.6,
                    'fill' => true,
                ],
                [
                    'label' => "Prediksi Bulan Depan ($nextMonthLabel)",
                    'data' => array_merge(array_fill(0, count($allMonths), null), [$this->predictNextMonthProfit($monthlyProfitData)]),
                    'backgroundColor' => 'rgba(255, 215, 0, 0.5)',
                    'borderColor' => 'rgba(255, 215, 0, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.6,
                    'fill' => true,
                ],
                // [
                //     'label' => 'Pengeluaran Bulanan', 
                //     'data' => $expensesValuesWithNull,
                //     'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                //     'borderColor' => 'rgba(255, 99, 132, 1)',
                //     'borderWidth' => 2,
                //     'tension' => 0.6,
                //     'fill' => true,
                // ],
            ],
            'labels' => $allLabels,
        ];
    }

    // Method for retrieving monthly profit data
    protected function getMonthlyProfitData(Carbon $startDate, Carbon $endDate): array
    {
        // Get total transaction incomes
        $transactionIncomes = $this->getMonthlyTotals(Transaction::incomes(), $startDate, $endDate);
        // Get total transaction payments incomes
        $transactionsIncomes = $this->getMonthlyTotals(TransactionPayments::incomes(), $startDate, $endDate);
        // Merge both income datasets
        $incomes = $this->mergeMonthlyData($transactionIncomes, $transactionsIncomes);

        // Get total transaction expenses
        $transactionsExpenses = $this->getMonthlyTotals(TransactionPayments::expenses(), $startDate, $endDate);
        // Merge expense data
        $expenses = $transactionsExpenses;

        // Get total employee payments (expenses)
        $employeePayments = $this->getMonthlyTotals(EmployeePayment::query(), $startDate, $endDate, 'payment_date');

        // Calculate monthly profits
        $months = collect(array_merge(array_keys($incomes), array_keys($expenses), array_keys($employeePayments)))
            ->unique()
            ->sort()
            ->take(4);

        $profits = [];
        foreach ($months as $month) {
            $profits[$month] = 
                ($incomes[$month] ?? 0) - 
                ($expenses[$month] ?? 0) - 
                ($employeePayments[$month] ?? 0);
        }

        return $profits;
    }

    // Method for retrieving monthly expenses data
    protected function getMonthlyExpensesData(Carbon $startDate, Carbon $endDate): array
    {
        // Get total transaction expenses
        $transactionsExpenses = $this->getMonthlyTotals(TransactionPayments::expenses(), $startDate, $endDate);

        // Get total employee payments (expenses related to employee payments)
        $employeePayments = $this->getMonthlyTotals(EmployeePayment::query(), $startDate, $endDate, 'payment_date');

        // Merge both datasets for total expenses
        return $this->mergeMonthlyData($transactionsExpenses, $employeePayments);
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

    // Method to get monthly totals
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

    // Method for predicting next month's profit based on linear regression
    protected function predictNextMonthProfit(array $profits): float
    {
        $profitValues = array_values($profits);

        // If data is less than 2 months, default prediction to average
        if (count($profitValues) < 2) {
            return array_sum($profitValues) / max(count($profitValues), 1);
        }

        $x = range(1, count($profitValues));
        $y = $profitValues;

        // Calculate linear regression
        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = array_sum(array_map(fn ($xi, $yi) => $xi * $yi, $x, $y));
        $sumX2 = array_sum(array_map(fn ($xi) => $xi ** 2, $x));

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX ** 2);
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Predict for the next month
        return $slope * ($n + 1) + $intercept;
    }

    // Method to define chart type
    protected function getType(): string
    {
        return 'bar';
    }
}
