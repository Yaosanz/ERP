<?php

namespace App\Filament\Resources\DivisionResource\Pages;

use App\Filament\Resources\DivisionResource;
use App\Filament\Resources\DivisionResource\Widgets\StatsDivision;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDivisions extends ListRecords
{
    protected static ?string $title = 'Halaman Divisi';
    protected static string $resource = DivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Data Divisi')
            ->Icon('heroicon-o-plus-circle'),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            StatsDivision::class,
        ];
    }
}
