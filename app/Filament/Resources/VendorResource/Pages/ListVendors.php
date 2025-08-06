<?php

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use App\Filament\Resources\VendorResource\Widgets\StatsVendor;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
class ListVendors extends ListRecords
{
    protected static ?string $title = 'Halaman Vendor';
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Data Vendor')
            ->Icon('heroicon-o-plus-circle'),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            StatsVendor::class,
        ];
    }
    public function getTabs(): array
    {
        return [
            'All' => Tab::make('Semua'),

            'active' => Tab::make('Aktif')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'Paid');
                }),

            'nactive' => Tab::make('Tidak Aktif')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'Unpaid');
                }),
        ];
    }
}
