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

        $combinedCounts = $categoryCountsTransaction->merge($categoryCountsTransactionPayments);

        $categoryCountSummed = $combinedCounts->groupBy('category_id')->map(fn ($group) => $group->sum('count'));

        $mostUsedCategoryId = $categoryCountSummed->sortDesc()->keys()->first();
        $maxCount = $categoryCountSummed->sortDesc()->first() ?? 0;

        $mostUsedCategoryName = $mostUsedCategoryId
            ? (Category::find($mostUsedCategoryId)?->name ?? 'Tidak diketahui')
            : 'Tidak ada';

        $categories = Category::orderBy('name')->get();

        $categoryCounts = $categories->map(function ($category) use ($categoryCountSummed) {
            return $categoryCountSummed->get($category->id, 0);
        })->toArray();

        $chartData = collect($categoryCounts)->sum() > 0
            ? $categoryCounts
            : [5, 4, 3, 2, 1]; 

        return [
            Stat::make('Total Kategori Bisnis', $totalBusinessModels)
                ->description('Total semua kategori bisnis di sistem')
                ->chart($chartData)
                ->color('primary'),

            Stat::make('Kategori Paling Sering Digunakan', $mostUsedCategoryName)
                ->description("Digunakan sebanyak $maxCount kali")
                ->chart($chartData)
                ->color('success'),
        ];
    }
}
