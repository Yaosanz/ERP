<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static ?string $title = 'Buat Data Karyawan';
    protected static string $resource = EmployeeResource::class;
    protected function afterSave(): void
    {
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
