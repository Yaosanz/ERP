<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
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

    
        $startDate = Carbon::parse($this->filters['startDate']);
        $endDate = Carbon::parse($this->filters['endDate']);


        $incomeData = $this->getDailyTotals(Transaction::incomes(), $startDate, $endDate);
        $expenseData = $this->getDailyTotals(Transaction::expenses(), $startDate, $endDate);


        $dates = $this->getDateRange($startDate, $endDate);

     
        $cumulativeVariance = 0;
        $varianceData = $dates->mapWithKeys(function ($date) use ($incomeData, $expenseData, &$cumulativeVariance) {
            $income = $incomeData[$date] ?? 0;
            $expense = $expenseData[$date] ?? 0;

            $dailyVariance = $income - $expense;
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

    protected function getDailyTotals($query, Carbon $startDate, Carbon $endDate): array
    {
        return $query
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->selectRaw('DATE(date_transaction) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    protected function getType(): string
    {
        return 'line'; 
    }
}
