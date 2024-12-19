<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CategoryResource\Widgets\StatsCategory;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCategories extends ListRecords
{
    protected static ?string $title = 'Halaman Bisnis Model';
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Data')
            ->Icon('heroicon-o-plus-circle'),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            StatsCategory::class,
        ];
    }
    public function getTabs(): array
    {
        return [
            'All' => Tab::make('All'),
    
            'Pemasukan' => Tab::make('Pemasukan')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('is_expense', false); // `false` untuk Pemasukan
                }),
    
            'Pengeluaran' => Tab::make('Pengeluaran')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('is_expense', true); // `true` untuk Pengeluaran
                }),
        ];
    }
    
}
