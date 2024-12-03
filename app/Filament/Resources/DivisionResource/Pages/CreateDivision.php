<?php

namespace App\Filament\Resources\DivisionResource\Pages;

use App\Filament\Resources\DivisionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDivision extends CreateRecord
{
    protected static string $resource = DivisionResource::class;
    protected static ?string $title = 'Buat Data Divisi';
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
