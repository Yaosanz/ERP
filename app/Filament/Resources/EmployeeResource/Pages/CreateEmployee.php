<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static ?string $title = 'Buat Data Karyawan';
    protected static string $resource = EmployeeResource::class;
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
