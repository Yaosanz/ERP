<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Doctrine\DBAL\Schema\Column;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Markdown;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'carbon-ibm-data-product-exchange';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Tambahkan Produk')
                ->description('Silahkan isi form berikut untuk menambahkan produk baru.')
                ->collapsible()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Produk') 
                        ->unique()
                        ->maxLength(20) 
                        ->minLength(3) 
                        ->required(),
                    Forms\Components\Select::make('category_id')
                        ->label('Kategori')
                        ->relationship('category', 'name')
                        ->required(),
                    Forms\Components\TextInput::make('price')
                        ->label('Harga')
                        ->maxLength(10)
                        ->minLength(3)
                        ->numeric()
                        ->required()
                        ->prefix('Rp.'),
                    Forms\Components\TextInput::make('stock')
                        ->label('Stok Barang')
                        ->numeric()
                        ->maxLength(7)
                        ->minLength(1)
                        ->required()
                        ->default(1),
                    Forms\Components\Select::make('unit')
                        ->label('Satuan Produk / Periode')
                        ->required()
                        ->options([
                            'Pcs' => 'Pieces',
                            'Kg' => 'Kilograms',
                            'Cm' => 'Centimeters',
                            'Unit' => 'Unit',
                            'Project' => 'Projects',
                            'Bulanan' => 'Monthly',
                            'Kuartalan' => 'Quarterly',
                            'Tahunan' => 'Yearly',
                        ]),
                    MarkdownEditor::make('description')->columnSpan('full')
                        ->label('Deskripsi Produk')
                        ->maxLength(255) 
                        ->minLength(3),
                ])->columnSpan(1)->columns(2),
                
                Section::make('Gambar')
                ->collapsible()
                ->schema([
                    Forms\Components\FileUpload::make('image')
                        ->label('Gambar Produk')
                        ->required()
                        ->image(),
                ])->columnSpan(1),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->prefix('Rp.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok Barang')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->label('Satuan Produk / Periode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(fn () => Category::pluck('name', 'id'))
                    ->placeholder('Semua Kategori'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function afterCreate(Form $form, Transaction $transaction): void
    {
        $product = Product::find($transaction->product_id);
        if ($product) {
            $product->stock -= $transaction->quantity;
            $product->save();
        }
    }
}
