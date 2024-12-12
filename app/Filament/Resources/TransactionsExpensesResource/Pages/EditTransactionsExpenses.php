<?php

namespace App\Filament\Resources\TransactionsExpensesResource\Pages;

use App\Filament\Resources\TransactionsExpensesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransactionsExpenses extends EditRecord
{
    protected static string $resource = TransactionsExpensesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
