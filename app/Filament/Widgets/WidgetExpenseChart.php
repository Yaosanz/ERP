<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\EmployeePayment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;

class WidgetExpenseChart extends ChartWidget
{
    use InteractsWithPageFilters;
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Pengeluaran';

    protected function getData(): array
    {
        
        if (empty($this->filters['startDate'] ?? null) || empty($this->filters['endDate'] ?? null)) {
            return [
                'datasets' => [
                    [
                        'label' => 'Pengeluaran per Hari',
                        'data' => [],
                        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',  
                        'borderColor' => 'rgba(255, 99, 132, 1)',       
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

        
        $startDate = Carbon::parse($this->filters['startDate']);
        $endDate = Carbon::parse($this->filters['endDate']);

    
        $expenses = Transaction::expenses()
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->selectRaw('DATE(date_transaction) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();


        $salaryExpenses = EmployeePayment::expenses()
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        
        $dates = $this->getDateRange($startDate, $endDate);
        $labels = $dates->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        })->toArray();

        
        $generalExpenseData = $this->getDailyTotals($expenses, $dates);
        $salaryExpenseData = $this->getDailyTotals($salaryExpenses, $dates);

        return [
            'datasets' => [
                [
                    'label' => 'Pengeluaran per Hari',
                    'data' => $generalExpenseData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.6,
                    'fill' => true,
                ],
                [
                    'label' => 'Pengeluaran Gaji Karyawan',
                    'data' => $salaryExpenseData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
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
                    ],
                ],
            ],
        ];
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

    protected function getDailyTotals($expenses, Collection $dates): array
    {
        $results = $expenses->pluck('total', 'date')->toArray();
        return $dates->map(function ($date) use ($results) {
            return $results[$date] ?? 0;
        })->toArray();
    }

    /**
     * @inheritDoc
     */
    protected function getType(): string
    {
        return 'line';
    }
}
