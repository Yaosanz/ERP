<?php

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVendor extends CreateRecord
{
    protected static ?string $title = 'Buat Data Vendor';
    protected static string $resource = VendorResource::class;
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }
}
