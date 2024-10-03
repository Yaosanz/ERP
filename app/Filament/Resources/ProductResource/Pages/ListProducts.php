<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ProductResource\Widgets\ProductStatsWidget;
use App\Filament\Resources\ProductResource\Widgets\SoldProductWidget;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\WidgetIncomeChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ProductStatsWidget::class,
        ];
    }
    protected function getFooterWidgets(): array
    {
        return [
        ];
    }
}
