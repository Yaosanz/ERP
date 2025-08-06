<?php

namespace App\Filament\Resources\BlogResource\Widgets;

use App\Models\Blog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsBlogs extends BaseWidget
{
    protected function getStats(): array
    {
        $totalBlogs = Blog::count();
        $publishedBlogs = Blog::where('published', true)->count();
        $unpublishedBlogs = $totalBlogs - $publishedBlogs;

        return [
            Stat::make('Total Blogs', $totalBlogs)
                ->description('Jumlah semua blog')
                ->descriptionIcon('heroicon-s-document-text')
                ->chart([1, 2, 3, 4, 5])
                ->color('primary'),
                
            Stat::make('Published Blogs', $publishedBlogs)
                ->description('Blog yang dipublikasikan')
                ->descriptionIcon('heroicon-s-check-circle')
                ->chart([1, 3, 5, 7, 9])
                ->color('success'),

            Stat::make('Unpublished Blogs', $unpublishedBlogs)
                ->description('Blog yang belum dipublikasikan')
                ->descriptionIcon('heroicon-s-x-circle')
                ->chart([1, 3, 5, 7, 9])
                ->color('danger'),
        ];
    }
}
