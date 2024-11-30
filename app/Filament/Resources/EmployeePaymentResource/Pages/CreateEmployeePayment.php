<?php

namespace App\Filament\Resources\EmployeePaymentResource\Pages;

use App\Filament\Resources\EmployeePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeePayment extends CreateRecord
{
    protected static string $resource = EmployeePaymentResource::class;
    protected static ?string $title = 'Buat Data Karyawan';
    protected function afterSave(): void
    {
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
