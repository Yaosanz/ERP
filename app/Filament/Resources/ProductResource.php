<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'carbon-ibm-data-product-exchange';
    protected static ?string $navigationGroup = "Manajemen";
    protected static ?string $navigationLabel = 'Kelola Produk';
    protected static ?int $navigationSort = 2;
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
                        ->prefix('Rp.')
                        ->helperText('Contoh: 20000000 = Rp. 20.000.000'),
                    Forms\Components\TextInput::make('stock')
                        ->label('Stok Barang')
                        ->numeric()
                        ->maxLength(7)
                        ->minLength(1)
                        ->default(1),
                    Forms\Components\Select::make('unit')
                        ->label('Satuan Produk Atau Periode')
                        ->required()
                        ->options([
                            'Pieces' => 'Pieces',
                            'Kilograms' => 'Kilograms',
                            'Centimeters' => 'Centimeters',
                            'Meter' => 'Meter',
                            'Unit' => 'Unit',
                            'Projects' => 'Projects',
                            'Monthly' => 'Monthly',
                            'Quarterly' => 'Quarterly',
                            'Yearly' => 'Yearly',
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
                        ->disk('public')
                            ->directory('products')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->previewable(),
                ])->columnSpan(1),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(function ($state) {
                        return 'Rp. ' . number_format($state, 0, ',', '.');
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok Barang')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->label('Satuan Produk / Periode')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Diubah')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['stock'] <= 0) {
            Notification::make()
                ->title('Stok Invalid')
                ->danger()
                ->body('Stok harus lebih besar dari 0.')
                ->send();

            throw ValidationException::withMessages([
                'stock' => 'Stok tidak valid.',
            ]);
        }

        return $data;
    }
}
