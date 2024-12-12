<?php

namespace App\Filament\Resources\ProductResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\Transaction;

class ProductStatsWidget extends BaseWidget
{
    protected function getColumns(): int
    {
        return 2; 
    }

    protected function getStats(): array
    {
        $totalStock = Product::sum('stock');
        $totalSold = Transaction::where('status', 'paid')->sum('quantity');

        return [
            Stat::make('Total Produk Tersisa', $totalStock)
                ->description('Stabil')
                ->chart([60,50,40,30,20,10,5])
                ->color('gray'),
                    
                Stat::make('Total Produk Terjual', $totalSold)
                ->description('Peningkatan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([5, 10, 20, 30, 40, 50, 60])
                ->color('success'),
        ];
    }
}
