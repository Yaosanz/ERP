<?php

namespace App\Filament\Exports;

use App\Models\TransactionPayments;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransactionPaymentsExporter extends Exporter
{
    protected static ?string $model = TransactionPayments::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('No'),
            ExportColumn::make('name')->label('Nama Transaksi'),
            ExportColumn::make('category.name')->label('Bisnis Model'),
            ExportColumn::make('amount')->label('Jumlah'),
            ExportColumn::make('quantity')->label('Kuantitas'),
            ExportColumn::make('date_transaction')->label('Tanggal Transaksi'),
            ExportColumn::make('description')->label('Deskripsi'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('vehicle_plate')->label('Plat Nomor Kendaraan'),
            ExportColumn::make('region')->label('Wilayah'),
            ExportColumn::make('created_at')->label('Dibuat Pada'),
            ExportColumn::make('updated_at')->label('Diperbarui Pada'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your transaction payments export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
