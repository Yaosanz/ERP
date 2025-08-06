<?php

namespace App\Filament\Resources\TransactionPaymentsResource\Pages;

use App\Filament\Resources\TransactionPaymentsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransactionPayments extends EditRecord
{
    protected static string $resource = TransactionPaymentsResource::class;
    protected static ?string $title = 'Edit Transaksi';
    protected function afterSave(): void
    {
        $this->redirect($this->getResource()::getUrl('index'));
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
