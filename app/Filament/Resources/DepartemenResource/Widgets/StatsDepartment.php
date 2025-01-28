<?php

namespace App\Filament\Resources\DepartemenResource\Widgets;

use App\Models\Departement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsDepartment extends BaseWidget
{
    protected function getColumns(): int
    {
        return 2; 
    }

    protected function getStats(): array
    {
        $totalDepartments = Departement::count() ?: 0;

        $mostEmployeesDepartment = Departement::withCount('employees')
            ->orderBy('employees_count', 'desc')
            ->first();

        return [

            Stat::make('Total Departments', $totalDepartments)
                ->description('Jumlah departemen yang terdaftar')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5]),

            Stat::make('Most Employees', $mostEmployeesDepartment ? $mostEmployeesDepartment->name : 'Tidak ada')
                ->description('Departemen dengan jumlah karyawan terbanyak')
                ->color('success')
                ->chart([1, 2, 3, 4, 5]),

        ];
    }
}
