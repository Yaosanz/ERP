<?php

namespace App\Filament\Resources\TransactionsIncomesResource\Pages;

use App\Filament\Resources\TransactionsIncomesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactionsIncomes extends ListRecords
{
    protected static string $resource = TransactionsIncomesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
