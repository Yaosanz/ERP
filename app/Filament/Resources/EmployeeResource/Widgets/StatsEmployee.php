<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsEmployee extends BaseWidget
{
    protected function getColumns(): int
    {
        return 2; 
    }

    protected function getStats(): array
    {
        $totalEmployees = Employee::count() ?: 0; 
        $maleEmployees = Employee::where('gender', 'male')->count() ?: 0; 
        $femaleEmployees = Employee::where('gender', 'female')->count() ?: 0; 
        $averageSalary = Employee::avg('salary') ?: 0; 

        return [
        
            Stat::make('Total Employees', $totalEmployees)
                ->description('Jumlah semua karyawan')
                ->descriptionIcon('heroicon-s-user-group')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5]), 

    
            Stat::make('Male Employees', $maleEmployees)
                ->description('Karyawan pria')
                ->descriptionIcon('heroicon-s-user-circle')
                ->color('info')
                ->chart([1, 3, 5, 7, 9]), 

            Stat::make('Female Employees', $femaleEmployees)
                ->description('Karyawan wanita')
                ->descriptionIcon('heroicon-s-user-circle')
                ->color('danger')
                ->chart([1, 4, 6, 8, 10]),
        
            Stat::make('Average Salary', 'Rp ' . number_format($averageSalary, 2, ',', '.'))
                ->description('Gaji rata-rata karyawan')
                ->descriptionIcon('heroicon-s-chart-bar')
                ->color('warning')
                ->chart([5, 10, 15, 20, 25]), 
        ];
    }
}
