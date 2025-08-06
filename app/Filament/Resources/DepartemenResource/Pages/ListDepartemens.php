<?php

namespace App\Filament\Resources\DepartemenResource\Pages;

use App\Filament\Resources\DepartemenResource;
use App\Filament\Resources\DepartemenResource\Widgets\StatsDepartment;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepartemens extends ListRecords
{
    protected static ?string $title = 'Halaman Departemen';
    protected static string $resource = DepartemenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Data Departemen')
            ->Icon('heroicon-o-plus-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StatsDepartment::class,
        ];
    }
}
