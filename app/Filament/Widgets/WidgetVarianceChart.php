<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\EmployeePayment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;

class WidgetVarianceChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Selisih dari Pemasukan dan Pengeluaran';
    protected static string $color = 'info';

    protected function getData(): array
    {
        if (empty($this->filters['startDate']) || empty($this->filters['endDate'])) {
            return $this->generateEmptyChart();
        }

        $startDate = Carbon::parse($this->filters['startDate']);
        $endDate = Carbon::parse($this->filters['endDate']);

        $incomeData = $this->getDailyTotals(Transaction::incomes(), $startDate, $endDate);
        $expenseData = $this->getDailyTotals(Transaction::expenses(), $startDate, $endDate);
        $employeePaymentData = $this->getDailyTotals(EmployeePayment::query(), $startDate, $endDate, 'payment_date'); // Ganti dengan 'payment_date'

        $dates = $this->getDateRange($startDate, $endDate);

        // Calculate cumulative variance (income - outcome - employee payments)
        $cumulativeVariance = 0;
        $varianceData = $dates->mapWithKeys(function ($date) use ($incomeData, $expenseData, $employeePaymentData, &$cumulativeVariance) {
            $income = $incomeData[$date] ?? 0;
            $expense = $expenseData[$date] ?? 0;
            $employeePayment = $employeePaymentData[$date] ?? 0;

            $dailyVariance = $income - $expense - $employeePayment;
            $cumulativeVariance += $dailyVariance;

            return [$date => $cumulativeVariance];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Cumulative Selisih Pemasukan dan Pengeluaran',
                    'data' => $varianceData->values()->toArray(),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.6,
                    'fill' => true,
                ],
            ],
            'labels' => $dates->toArray(),
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
                    ],
                ],
            ],
        ];
    }

    protected function getDailyTotals($query, Carbon $startDate, Carbon $endDate, $dateColumn = 'date_transaction'): array
    {
        return $query
            ->whereBetween($dateColumn, [$startDate, $endDate])
            ->selectRaw("DATE($dateColumn) as date, SUM(amount) as total")
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();
    }

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

    protected function generateEmptyChart()
    {
        return [
            'datasets' => [
                [
                    'label' => 'Cumulative Selisih Pemasukan dan Pengeluaran',
                    'data' => [],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
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
                    ],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
