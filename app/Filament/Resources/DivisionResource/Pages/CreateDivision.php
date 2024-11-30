<?php

namespace App\Filament\Resources\DivisionResource\Pages;

use App\Filament\Resources\DivisionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDivision extends CreateRecord
{
    protected static string $resource = DivisionResource::class;
    protected static ?string $title = 'Buat Data Divisi';
    protected function afterSave(): void
    {
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
