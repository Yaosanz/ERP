<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\TransactionPayments;
use App\Models\TransactionsIncomes;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Collection;

class WidgetIncomeChart extends ChartWidget
{
    use InteractsWithPageFilters;
    
    protected static ?int $sort = 1;
    protected static ?string $heading = 'Pemasukan';
    protected static bool $isLazy = false;

    protected function getData(): array
    {
        if (empty($this->filters['startDate'] ?? null) || empty($this->filters['endDate'] ?? null)) {
            return [
                'datasets' => [
                    [
                        'label' => 'Pemasukan per Hari',
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

        // Fetch incomes from Transaction model and TransactionsIncomes model
        $transactions = Transaction::incomes()
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->selectRaw('DATE(date_transaction) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $transactionsIncomes = TransactionPayments::whereBetween('date_transaction', [$startDate, $endDate])
            ->selectRaw('DATE(date_transaction) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = $this->getDateRange($startDate, $endDate);
        $labels = $dates->map(function ($date) {
            return Carbon::parse($date)->format('Y-m-d');
        })->toArray();

        $dataTransactions = $this->getDailyTotals($transactions, $dates);
        $dataTransactionsIncomes = $this->getDailyTotals($transactionsIncomes, $dates);

        // Combine both datasets for chart
        return [
            'datasets' => [
                [
                    'label' => 'Transaction (Products)',
                    'data' => $dataTransactions,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',  
                    'borderColor' => 'rgba(75, 192, 192, 1)',       
                    'borderWidth' => 2,  
                    'tension' => 0.6,    
                    'fill' => true,      
                ],
                [
                    'label' => 'Transactions (General)',
                    'data' => $dataTransactionsIncomes,
                    'backgroundColor' => 'rgba(153, 102, 255, 0.2)',  
                    'borderColor' => 'rgba(153, 102, 255, 1)',       
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

    protected function getDailyTotals($incomes, Collection $dates): array
    {
        $results = $incomes->pluck('total', 'date')->toArray();
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
