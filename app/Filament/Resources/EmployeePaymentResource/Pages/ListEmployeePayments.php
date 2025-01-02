<?php

namespace App\Filament\Resources\EmployeePaymentResource\Pages;

use App\Filament\Resources\EmployeePaymentResource;
use App\Filament\Resources\EmployeePaymentResource\Widgets\EmployeePaymentStatsWdiget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEmployeePayments extends ListRecords
{
    protected static ?string $title = 'Halaman Transaksi Gaji Karyawan';
    protected static string $resource = EmployeePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Data Transaksi')
            ->Icon('heroicon-o-plus-circle'),
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

    protected function getHeaderWidgets(): array
    {
        return [
            EmployeePaymentStatsWdiget::class,
        ];
    }
}
