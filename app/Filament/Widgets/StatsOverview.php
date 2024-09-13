<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    
    protected function getStats(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            null;

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();
        
        $income = Transaction::incomes()
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');
            
        $outcome = Transaction::expenses()
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        return [
            Stat::make('Total Pemasukan', 'Rp. ' . number_format($income))
                ->description('Peningkatan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([1000, 2000])
                ->color('success'),
            Stat::make('Total Pengeluaran', 'Rp. ' . number_format($outcome))
                ->description('Penurunan')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([2000, 1000])
                ->color('danger'),
            Stat::make('Selisih', 'Rp. ' . number_format($income - $outcome))
                ->description('Laba Perusahaan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([1000, 2000])
                ->color('success'),
        ];
    }
}
