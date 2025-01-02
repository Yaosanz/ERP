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

        $departmentCounts = Departement::withCount('employees')
            ->get()
            ->pluck('employees_count')
            ->toArray();

        return [

            Stat::make('Total Departments', $totalDepartments)
                ->description('Jumlah departemen yang terdaftar')
                ->color('primary')
                ->chart([1, 2, 3, 4, 5]),

            Stat::make('Most Employees', $mostEmployeesDepartment?->name ?? 'None')
                ->description($mostEmployeesDepartment ? $mostEmployeesDepartment->employees_count . ' employees' : 'No data')
                ->color('success')
                ->chart($departmentCounts), 

        ];
    }
}
