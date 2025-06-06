<?php

namespace App\Filament\Widgets;

use App\Models\EmployeePayment;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $startDate = !is_null($this->filters['startDate'] ?? null) 
            ? Carbon::parse($this->filters['startDate']) 
            : null;

        $endDate = !is_null($this->filters['endDate'] ?? null) 
            ? Carbon::parse($this->filters['endDate']) 
            : null;

        $income = Transaction::incomes()
            ->where('status', 'Paid')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        $outcome = Transaction::expenses()
            ->where('status', 'Paid')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        $employeePayments = EmployeePayment::where('status', 'Paid')
            ->whereBetween('payment_date', [$startDate, $endDate]) 
            ->sum('amount');

        $profit = $income - $outcome - $employeePayments;

        return [
            Stat::make('Total Pemasukan', 'Rp. ' . number_format($income))
                ->description('Peningkatan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($this->generateDynamicChart($income, true))
                ->color($income >= 0 ? 'success' : 'danger'),

            Stat::make('Total Pengeluaran', 'Rp. ' . number_format($outcome))
                ->description('Penurunan')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart($this->generateDynamicChart($outcome, false))
                ->color($outcome >= 0 ? 'danger' : 'success'),

            Stat::make('Total Pembayaran Karyawan', 'Rp. ' . number_format($employeePayments))
                ->description('Pembayaran Bulan Ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($this->generateDynamicChart($employeePayments, true))
                ->color('info'),

            Stat::make('Selisih', 'Rp. ' . number_format($profit))
                ->description('Laba Perusahaan')
                ->descriptionIcon($profit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($this->generateDynamicChart($profit, $profit >= 0))
                ->color($profit >= 0 ? 'success' : 'danger'),
        ];
    }

    protected function generateDynamicChart($amount, $isPositive)
    {
        $chartValues = $isPositive ? [
            $amount * 0.1,  
            $amount * 0.25, 
            $amount * 0.5,  
            $amount * 0.75, 
            $amount * 1.0,  
            $amount * 1.5, 
            $amount * 2.0, 
        ] : [
            $amount * 2.0,  
            $amount * 1.5,  
            $amount * 1.0,  
            $amount * 0.75, 
            $amount * 0.5,  
            $amount * 0.25, 
            $amount * 0.1, 
        ];
    
        return $chartValues;
    }    
}