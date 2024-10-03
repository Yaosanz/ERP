<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now();

        $income = Transaction::incomes()
            ->where('status', 'Paid')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        $outcome = Transaction::expenses()
            ->where('status', 'Paid')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        $profit = $income - $outcome;

        return [
            Stat::make('Total Pemasukan', 'Rp. ' . number_format($income))
                ->description('Peningkatan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Total Pengeluaran', 'Rp. ' . number_format($outcome))
                ->description('Penurunan')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([17, 16, 14, 15, 14, 13, 12])
                ->color('danger'),

            Stat::make('Selisih', 'Rp. ' . number_format($profit))
                ->description('Laba Perusahaan')
                ->descriptionIcon($profit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart([15, 4, 10, 2, 12, 4, 12])
                ->color($profit >= 0 ? 'success' : 'danger'),
        ];
    }
}
