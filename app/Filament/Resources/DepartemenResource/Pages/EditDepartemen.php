<?php

namespace App\Filament\Resources\DepartemenResource\Pages;

use App\Filament\Resources\DepartemenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepartemen extends EditRecord
{
    protected static string $resource = DepartemenResource::class;
    protected static ?string $title = 'Edit Data Departemen';
    protected function afterSave(): void
    {
        $this->redirect($this->getResource()::getUrl('index'));
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
