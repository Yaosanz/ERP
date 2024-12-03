<?php

namespace App\Filament\Resources\EmployeePaymentResource\Pages;

use App\Filament\Resources\EmployeePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeePayment extends EditRecord
{
    protected static string $resource = EmployeePaymentResource::class;
    protected static ?string $title = 'Edit Data Pembayaran Karyawan';
    protected function afterSave(): void
    {
        $this->redirect($this->getResource()::getUrl('index'));
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
