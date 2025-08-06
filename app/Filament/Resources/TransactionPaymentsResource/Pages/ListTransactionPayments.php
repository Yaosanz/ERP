<?php

namespace App\Filament\Resources\TransactionPaymentsResource\Pages;

use App\Filament\Resources\TransactionPaymentsResource;
use App\Filament\Resources\TransactionResource\Widgets\TransactionStatsWidget;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListTransactionPayments extends ListRecords
{
    protected static string $resource = TransactionPaymentsResource::class;
    protected static ?string $title = 'Halaman Transaksi General';

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
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
