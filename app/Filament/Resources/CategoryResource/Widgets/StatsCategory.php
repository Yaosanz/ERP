<?php

namespace App\Filament\Resources\CategoryResource\Widgets;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionPayments;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsCategory extends BaseWidget
{
    protected static bool $isLazy = false;
    protected function getColumns(): int
    {
        return 2; 
    }

    protected function getStats(): array
    {
        $totalBusinessModels = Category::count();

        $categoryCountsTransactionPayments = TransactionPayments::select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->get();

        $categoryCountsTransaction = Transaction::select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->get();

        $categoryCounts = $categoryCountsTransaction->merge($categoryCountsTransactionPayments);

        $categoryCountSummed = $categoryCounts->groupBy('category_id')->map(function ($group) {
            return $group->sum('count');
        });

        $mostUsedCategory = $categoryCountSummed->sortDesc()->keys()->first();
        $maxCount = $categoryCountSummed->sortDesc()->first();

        $mostUsedCategoryName = $mostUsedCategory ? Category::find($mostUsedCategory)->name : 'Tidak ada';

        $categoryData = Category::all()->map(function ($category) use ($categoryCountSummed) {
            $combinedCount = $categoryCountSummed->get($category->id, 0);

            return [
                'name' => $category->name,
                'count' => $combinedCount,
            ];
        });

        $categoryCounts = $categoryData->pluck('count')->toArray();

        return [
            Stat::make('Total Kategori Bisnis', $totalBusinessModels)
                ->description('Jumlah total kategori bisnis yang ada dalam sistem')
                ->chart($categoryCounts)
                ->color('warning'),

            Stat::make('Kategori Bisnis Paling Sering Digunakan', $mostUsedCategoryName)
                ->description("Digunakan $maxCount kali secara keseluruhan")
                ->chart($categoryCounts)
                ->color('success'),
        ];
    }
}
