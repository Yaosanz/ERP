<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
    protected static ?string $title = 'Buat Data Model Bisnis';
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
