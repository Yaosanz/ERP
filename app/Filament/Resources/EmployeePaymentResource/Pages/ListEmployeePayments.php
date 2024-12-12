<?php

namespace App\Filament\Resources\EmployeePaymentResource\Pages;

use App\Filament\Resources\EmployeePaymentResource;
use App\Filament\Resources\EmployeePaymentResource\Widgets\EmployeePaymentStatsWdiget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeePayments extends ListRecords
{
    protected static ?string $title = 'Halaman Pembayaran Gaji Karyawan';
    protected static string $resource = EmployeePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Data Pembayaran')
            ->Icon('heroicon-o-plus-circle'),
        ];
        
    }
    protected function getHeaderWidgets(): array
    {
        return [
            EmployeePaymentStatsWdiget::class,
        ];
    }
}
