<?php

namespace App\Filament\Resources\EmployeePaymentResource\Pages;

use App\Filament\Resources\EmployeePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeePayment extends EditRecord
{
    protected static string $resource = EmployeePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
