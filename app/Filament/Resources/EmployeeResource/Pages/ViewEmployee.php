<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;
    protected static ?string $title = 'Halaman Karyawan';
   
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back_to_index')
                ->label('Kembali ke Daftar Karyawan')
                ->icon('heroicon-o-arrow-left')
                ->color('warning')
                ->url(EmployeeResource::getUrl()), 
        ];
    }
}
