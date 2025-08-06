<?php

namespace App\Filament\Resources\VendorResource\Widgets;

use App\Models\Vendor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsVendor extends BaseWidget
{
    protected function getColumns(): int
    {
        return 3; 
    }

    protected function getStats(): array
    {
        $totalVendors = Vendor::count(); 
        $activeVendors = Vendor::where('status', 'active')->count(); 
        $inactiveVendors = Vendor::where('status', 'inctive')->count(); 

        return [
            Stat::make('Total Vendor', $totalVendors)
                ->description('Jumlah vendor terdaftar')
                ->chart([1, 2, 3, 4, 5])
                ->color('primary'),
                
            Stat::make('Vendor Aktif', $activeVendors)
                ->description('Vendor dengan status aktif')
                ->chart([1, 3, 5, 7, 9])
                ->color('warning'),
            Stat::make('Vendor Tidak Aktif', $inactiveVendors)
                ->description('Vendor dengan status aktif')
                ->chart([1, 3, 5, 7, 9])
                ->color('gray'),
        ];
    }
}
