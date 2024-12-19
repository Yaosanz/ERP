<?php

namespace App\Filament\Resources\TransactionPaymentsResource\Pages;

use App\Filament\Resources\TransactionPaymentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransactionPayments extends ViewRecord
{
    protected static string $resource = TransactionPaymentsResource::class;
    protected static ?string $title = 'Halaman Transaksi General';
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
