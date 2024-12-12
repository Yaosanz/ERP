<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource\Widgets\TransactionStatsWidget;
use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTransactions extends ListRecords
{
    protected static ?string $title = 'Halaman Pembayaran Produk';
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Data Pembayaran Produk')
            ->Icon('heroicon-o-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TransactionStatsWidget::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make('All'),

            'Paid' => Tab::make('Paid')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'Paid');
                }),

            'Unpaid' => Tab::make('Unpaid')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'Unpaid');
                }),

            'Pending' => Tab::make('Pending')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'Pending');
                }),

            'Cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('status', 'Cancelled');
                }),
        ];
    }
}
