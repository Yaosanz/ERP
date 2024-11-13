<?php

namespace App\Filament\Resources\MarketConditionsResource\Pages;

use App\Filament\Resources\MarketConditionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMarketConditions extends ListRecords
{
    protected static string $resource = MarketConditionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
