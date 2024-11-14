<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use App\Models\EmployeePayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        // Fetching income data (all transactions with "Paid" status)
        $income = Transaction::incomes()
            ->where('status', 'Paid')
            ->sum('amount');

        // Fetching outcome data (all expenses with "Paid" status)
        $outcome = Transaction::expenses()
            ->where('status', 'Paid')
            ->sum('amount');

        // Employee payment data for all records with "Paid" status
        $employeePayments = EmployeePayment::where('status', 'Paid')->sum('amount');

        // Calculate profit
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
                ->description('Pembayaran Karyawan')
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
