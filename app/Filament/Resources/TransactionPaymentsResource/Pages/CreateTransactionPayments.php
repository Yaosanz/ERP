<?php

namespace App\Filament\Resources\TransactionPaymentsResource\Pages;

use App\Filament\Resources\TransactionPaymentsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransactionPayments extends CreateRecord
{
    protected static string $resource = TransactionPaymentsResource::class;
    protected static ?string $title = 'Buat Data Transaksi';

    /**
     * Redirect ke halaman index setelah data berhasil dibuat.
     */
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
