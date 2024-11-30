<?php

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVendor extends CreateRecord
{
    protected static ?string $title = 'Buat Data Vendor';
    protected static string $resource = VendorResource::class;
    protected function afterSave(): void
    {
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
