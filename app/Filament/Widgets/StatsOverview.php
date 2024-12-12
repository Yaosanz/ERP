<?php

namespace App\Filament\Widgets;

use App\Models\EmployeePayment;
use App\Models\Transaction;
use App\Models\TransactionsExpense;
use App\Models\TransactionsIncomes;
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
        // Default to null if filters are empty or not set
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        // Parse to Carbon if filters exist
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Total income
        $income = Transaction::incomes()
            ->where('status', 'Paid')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        // Add income from TransactionsIncomes
        $income += TransactionsIncomes::where('status', 'Paid')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        // Total outcome
        $outcome = Transaction::expenses()
            ->where('status', 'Paid')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        // Add expenses from TransactionsExpense
        $outcome += TransactionsExpense::where('status', 'Paid')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        // Employee payments
        $employeePayments = EmployeePayment::where('status', 'Paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        // Calculate profit
        $profit = $income - $outcome - $employeePayments;

        // Refresh chart if no filters are applied
        $profitValuesWithNull = $this->generateDynamicChart($profit, $profit >= 0);

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
                ->chart($profitValuesWithNull)
                ->color($profit >= 0 ? 'success' : 'danger'),
        ];
    }

    // Generates dynamic chart values based on the given amount and positive/negative status
    protected function generateDynamicChart($amount, $isPositive)
    {
        if ($amount === 0) {
            return [0, 0, 0, 0, 0, 0, 0];
        }

        return $isPositive ? [
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
    }
}
