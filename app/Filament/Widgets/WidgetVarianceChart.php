<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\TransactionsIncomes;
use App\Models\TransactionsExpense;
use App\Models\EmployeePayment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;

class WidgetVarianceChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;
    protected static ?string $heading = 'Selisih Keuntungan';
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        // Cek jika filter tanggal tidak ada
        if (empty($this->filters['startDate']) || empty($this->filters['endDate'])) {
            return [
                'datasets' => [
                    [
                        'label' => 'Variance per Hari',
                        'data' => [],
                        'backgroundColor' => 'rgba(0, 255, 0, 0.2)',  // Green color with transparency
                        'borderColor' => 'rgba(0, 255, 0, 1)',  // Green border
                        'borderWidth' => 2,
                        'tension' => 0.6,
                        'fill' => true,
                    ],
                ],
                'labels' => [],
                'options' => [
                    'animation' => [
                        'duration' => 3000,
                        'easing' => 'easeInOutSine',
                    ],
                    'elements' => [
                        'line' => [
                            'tension' => 0.6,
                        ],
                    ],
                    'scales' => [
                        'y' => [
                            'beginAtZero' => true,
                            'ticks' => [
                                'color' => '#4CAF50',  // Green color for Y-axis ticks
                            ],
                        ],
                        'x' => [
                            'ticks' => [
                                'color' => '#4CAF50',  // Green color for X-axis ticks
                            ],
                        ],
                    ],
                ],
            ];
        }

        $startDate = Carbon::parse($this->filters['startDate']);
        $endDate = Carbon::parse($this->filters['endDate']);

        // Mengambil data transaksi dari model Transaction
        $transactions = Transaction::whereBetween('date_transaction', [$startDate, $endDate])
            ->selectRaw('DATE(date_transaction) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('Y-m-d');
                return $item;
            });

        // Mengambil data transaksi dari model TransactionsIncomes
        $transactionsIncomes = TransactionsIncomes::whereBetween('date_transaction', [$startDate, $endDate])
            ->selectRaw('DATE(date_transaction) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('Y-m-d');
                return $item;
            });

        // Mengambil data pengeluaran dari model TransactionsExpense
        $expenses = TransactionsExpense::expenses()
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->selectRaw('DATE(date_transaction) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('Y-m-d');
                return $item;
            });

        // Mengambil data pengeluaran gaji dari model EmployeePayment
        $salaryExpenses = EmployeePayment::expenses()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('Y-m-d');
                return $item;
            });

        // Mendapatkan rentang tanggal
        $dates = $this->getDateRange($startDate, $endDate);
        $labels = $dates->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))->toArray();

        // Mengambil total harian dari masing-masing data
        $incomeData = $this->getDailyTotals($transactions, $dates);
        $transactionsIncomeData = $this->getDailyTotals($transactionsIncomes, $dates);
        $expenseData = $this->getDailyTotals($expenses, $dates);
        $salaryExpenseData = $this->getDailyTotals($salaryExpenses, $dates);

        // Menghitung varians (pemasukan - pengeluaran - pengeluaran gaji) secara kumulatif per hari
        $varianceData = [];
        $runningTotal = 0; // Untuk menghitung selisih kumulatif per hari

        foreach ($dates as $index => $date) {
            // Akumulasi dari pemasukan dan pengeluaran
            $income = $incomeData[$index] ?? 0;
            $transactionIncome = $transactionsIncomeData[$index] ?? 0;
            $expense = $expenseData[$index] ?? 0;
            $salaryExpense = $salaryExpenseData[$index] ?? 0;

            // Perhitungan varians untuk hari ini
            $dailyVariance = $income + $transactionIncome - ($expense + $salaryExpense);
            $runningTotal += $dailyVariance; // Mengakumulasi varians

            // Menyimpan hasil perhitungan varians kumulatif
            $varianceData[] = $runningTotal;
        }

        // Menggabungkan semua dataset untuk chart
        return [
            'datasets' => [
                [
                    'label' => 'Profits (Incomes - Expenses)',
                    'data' => $varianceData,
                    'backgroundColor' => 'rgba(76, 175, 80, 0.2)', // Green with transparency
                    'borderColor' => 'rgba(76, 175, 80, 1)', // Green border
                    'borderWidth' => 2,
                    'tension' => 0.6,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
            'options' => [
                'animation' => [
                    'duration' => 3000,
                    'easing' => 'easeInOutSine',
                ],
                'elements' => [
                    'line' => [
                        'tension' => 0.6,
                    ],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'color' => '#4CAF50',  // Green color for Y-axis ticks
                        ],
                    ],
                    'x' => [
                        'ticks' => [
                            'color' => '#4CAF50',  // Green color for X-axis ticks
                        ],
                    ],
                ],
            ],
        ];
    }

    // Mendapatkan rentang tanggal antara startDate dan endDate
    protected function getDateRange(Carbon $startDate, Carbon $endDate): Collection
    {
        $dates = collect();
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dates->push($currentDate->format('Y-m-d'));
            $currentDate->addDay();
        }

        return $dates;
    }

    // Mengambil total per hari dari data yang diberikan
    protected function getDailyTotals($modelData, Collection $dates): array
    {
        $results = $modelData->pluck('total', 'date')->toArray();
        return $dates->map(function ($date) use ($results) {
            return $results[$date] ?? 0;
        })->toArray();
    }

    /**
     * @inheritDoc
     */
    protected function getType(): string
    {
        return 'line'; // Menyajikan chart dalam bentuk line chart
    }
}
