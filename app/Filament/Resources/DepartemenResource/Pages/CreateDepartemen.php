<?php

namespace App\Filament\Resources\DepartemenResource\Pages;

use App\Filament\Resources\DepartemenResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDepartemen extends CreateRecord
{
    protected static string $resource = DepartemenResource::class;
    protected static ?string $title = 'Buat Data Departemen';
    protected function afterSave(): void
    {
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
