<?php

namespace App\Filament\Exports;

use App\Models\Transaction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransactionExporter extends Exporter
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('No'),
            ExportColumn::make('name')->label('Nama Transaksi'),
            ExportColumn::make('category.name')->label('Bisnis Model'),
            ExportColumn::make('product.name')->label('Nama Produk'),
            ExportColumn::make('date_transaction')->label('Tanggal Transaksi'),
            ExportColumn::make('product_name')->label('Nama Produk'),
            ExportColumn::make('quantity')->label('Jumlah'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('amount')->label('Jumlah'),
            ExportColumn::make('description')->label('Deskripsi'),
            ExportColumn::make('created_at')->label('Dibuat Pada'),
            ExportColumn::make('updated_at')->label('Diperbarui Pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your transaction export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
