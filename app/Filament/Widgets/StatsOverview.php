<?php

namespace App\Filament\Widgets;

use App\Models\EmployeePayment;
use App\Models\Transaction;
use App\Models\TransactionPayments;
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
        $startDate = isset($this->filters['startDate']) ? Carbon::parse($this->filters['startDate']) : null;
        $endDate = isset($this->filters['endDate']) ? Carbon::parse($this->filters['endDate']) : null;

        $incomeFromProducts = Transaction::incomes()
            ->where('status', 'Paid')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        $incomeFromOtherSources = TransactionPayments::incomes()
            ->where('status', 'Paid')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        $totalIncome = $incomeFromProducts + $incomeFromOtherSources;

        $totalOutcome = TransactionPayments::expenses()
            ->where('status', 'Paid')
            ->whereBetween('date_transaction', [$startDate, $endDate])
            ->sum('amount');

        $employeePayments = EmployeePayment::where('status', 'Paid')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        $profit = $totalIncome - $totalOutcome - $employeePayments;

        return [
            Stat::make('Total Pemasukan', 'Rp. ' . number_format($totalIncome))
                ->description('Peningkatan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($this->generateDynamicChart($totalIncome, true))
                ->color($totalIncome >= 0 ? 'success' : 'danger'),

            Stat::make('Total Pengeluaran', 'Rp. ' . number_format($totalOutcome+$employeePayments))
                ->description('Penurunan')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart($this->generateDynamicChart($totalOutcome, false))
                ->color('danger'),

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
        if (!$amount || $amount === 0) {
            return [0, 0, 0, 0, 0, 0, 0];
        }

        return $isPositive
            ? [
                $amount * 0.1,
                $amount * 0.25,
                $amount * 0.5,
                $amount * 0.75,
                $amount,
                $amount * 1.5,
                $amount * 2,
            ]
            : [
                $amount * 2,
                $amount * 1.5,
                $amount,
                $amount * 0.75,
                $amount * 0.5,
                $amount * 0.25,
                $amount * 0.1,
            ];
    }

   
}
