<?php

namespace App\Filament\Imports;

use App\Models\Product;
use App\Models\Transaction;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TransactionImporter extends Importer
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping(),
            ImportColumn::make('date_transaction')
                ->requiredMapping(),
            ImportColumn::make('product_name')
                ->requiredMapping(),
            ImportColumn::make('status')
                ->requiredMapping(),
            ImportColumn::make('amount')
                ->requiredMapping()
                ->numeric(),
            ImportColumn::make('description')
                ->requiredMapping(),
        ];
    }

    public function resolveRecord(): ?Transaction
    {
        $transaction = Transaction::firstOrNew([
            'name' => $this->data['name'],
            'date_transaction' => $this->data['date_transaction'],
        ]);
    $product = Product::where('name', $this->data['product_name'])->first();
    if ($product) {
        $transaction->product_id = $product->id;
    }
        $transaction->product_name = $this->data['product_name'];
        $transaction->status = $this->data['status'];
        $transaction->amount = $this->data['amount'];
        $transaction->description = $this->data['description'];

        $transaction->save();

        return $transaction;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your transaction import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
