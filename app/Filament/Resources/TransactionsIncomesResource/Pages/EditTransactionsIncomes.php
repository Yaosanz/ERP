<?php

namespace App\Filament\Resources\TransactionsIncomesResource\Pages;

use App\Filament\Resources\TransactionsIncomesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransactionsIncomes extends EditRecord
{
    protected static string $resource = TransactionsIncomesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
