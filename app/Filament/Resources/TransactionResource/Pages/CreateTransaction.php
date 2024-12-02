<?php

namespace App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class CreateTransaction extends CreateRecord
{
    protected static ?string $title = 'Buat Data Transaksi';
    protected static string $resource = TransactionResource::class;

    /**
     * Redirect ke halaman index setelah data berhasil dibuat.
     */
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $product = Product::find($data['product_id']);
        
        if ($product && $product->stock < $data['quantity']) {
            Notification::make()
                ->title('Stok Tidak Cukup')
                ->danger()
                ->body('Stok produk "' . $product->name . '" tidak mencukupi. Transaksi dibatalkan.')
                ->send();

            // Lempar validasi untuk menghentikan proses
            throw ValidationException::withMessages([
                'quantity' => 'Stok tidak mencukupi untuk jumlah yang diminta.',
            ]);
        }

        return $data;
    }

}
