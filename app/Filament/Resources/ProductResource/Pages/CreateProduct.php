<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static ?string $title = 'Buat Data Produk';
    protected static string $resource = ProductResource::class;
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
