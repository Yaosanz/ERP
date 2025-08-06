<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Vendor;

class ProductStatsWidget extends BaseWidget
{
    protected function getColumns(): int
    {
        return 4; 
    }

    protected function getStats(): array
    {
        $totalStock = Product::sum('stock');
        $totalSold = Transaction::where('status', 'paid')->sum('quantity');
        $totalVendors = Vendor::count(); 
        $activeVendors = Vendor::where('status', 'active')->count(); // Vendor yang aktif

        return [
            Stat::make('Total Produk Tersisa', $totalStock)
                ->description('Stabil')
                ->chart([50,50,50,50])
                ->color('gray'),
                    
            Stat::make('Total Produk Terjual', $totalSold)
                ->description('Peningkatan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([5, 10, 20, 30, 40, 50, 60])
                ->color('success'),

            Stat::make('Total Vendor', $totalVendors)
                ->description('Jumlah vendor terdaftar')
                ->chart([1, 2, 3, 4, 5])
                ->color('primary'),
                
            Stat::make('Vendor Aktif', $activeVendors)
                ->description('Vendor dengan status aktif')
                ->chart([1, 3, 5, 7, 9])
                ->color('info'),
        ];
    }
}
