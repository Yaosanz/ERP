<?php

namespace App\Filament\Resources\DivisionResource\Widgets;

use App\Models\Division;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsDivision extends BaseWidget
{
    protected static bool $isLazy = false;
    
    protected function getColumns(): int
    {
        return 2; 
    }

    protected function getStats(): array
    {
        $totalDivisions = Division::count();

     
        $divisionEmployeeCounts = Employee::select('divisions_id', DB::raw('count(*) as count'))
            ->groupBy('divisions_id')
            ->get();

       
        $divisionData = Division::all()->map(function ($division) use ($divisionEmployeeCounts) {
            $employeeCount = $divisionEmployeeCounts->firstWhere('divisions_id', $division->id)->count ?? 0;

            return [
                'name' => $division->division_name,
                'count' => $employeeCount,
            ];
        });

        $divisionCounts = $divisionData->pluck('count')->toArray();

   
        $mostEmployeesDivision = $divisionData->sortByDesc('count')->first();
        $mostEmployeesDivisionName = $mostEmployeesDivision ? $mostEmployeesDivision['name'] : 'Tidak ada';
        $maxEmployeeCount = $mostEmployeesDivision ? $mostEmployeesDivision['count'] : 0;

        return [
            Stat::make('Total Divisi', $totalDivisions)
                ->description('Jumlah total divisi dalam perusahaan')
                ->chart($divisionCounts)
                ->color('primary'),

            Stat::make('Divisi dengan Karyawan Terbanyak', $mostEmployeesDivisionName)
                ->description("Memiliki $maxEmployeeCount karyawan")
                ->chart($divisionCounts)
                ->color('success'),
        ];
    }
}
