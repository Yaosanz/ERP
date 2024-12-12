<?php

namespace App\Filament\Resources\EmployeePaymentResource\Pages;

use App\Filament\Resources\EmployeePaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeePayment extends CreateRecord
{
    protected static string $resource = EmployeePaymentResource::class;
    protected static ?string $title = 'Buat Data Pembayaran Gaji Karyawan';
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
