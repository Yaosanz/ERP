<?php

namespace App\Filament\Resources\MarketConditionsResource\Pages;

use App\Filament\Resources\MarketConditionsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMarketConditions extends EditRecord
{
    protected static string $resource = MarketConditionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
