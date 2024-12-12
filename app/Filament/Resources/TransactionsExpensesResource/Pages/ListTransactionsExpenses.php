<?php

namespace App\Filament\Resources\TransactionsExpensesResource\Pages;

use App\Filament\Resources\TransactionsExpensesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactionsExpenses extends ListRecords
{
    protected static string $resource = TransactionsExpensesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
