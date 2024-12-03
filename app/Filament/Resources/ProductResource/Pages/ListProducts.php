<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource\Widgets\ProductStatsWidget;
use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListProducts extends ListRecords
{
    protected static ?string $title = 'Halaman Produk';
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah Data Produk')
            ->Icon('heroicon-o-plus-circle'),
        ];
    }

    public function mount(): void
    {
        parent::mount();

        $lowStockProducts = Product::where('stock', '<=', 10)->get(); 

        if ($lowStockProducts->isNotEmpty()) {
            $message = $lowStockProducts->map(fn($product) => $product->name . ' (Stok: ' . $product->stock . ')')->join(', ');

            Notification::make()
                ->title('Peringatan Stok Hampir Habis')
                ->warning()
                ->body('Produk berikut memiliki stok hampir habis: ' . $message)
                ->send();
        }
    }
    protected function getHeaderWidgets(): array
    {
        return [
            ProductStatsWidget::class,
        ];
    }
    protected function getFooterWidgets(): array
    {
        return [
        ];
    }
}
