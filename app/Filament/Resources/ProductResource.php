<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;  // Import Model Vendor
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
                            ->required()
                            ->helperText('Masukkan nama produk yang ingin ditambahkan.'),
                        Forms\Components\Select::make('vendor_id') 
                            ->label('Vendor')
                            ->relationship('vendor', 'item') 
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih vendor yang menyediakan produk ini (Opsional).'),
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
                            ->default(1)
                            ->required()
                            ->helperText('Masukkan jumlah stok barang yang tersedia.'),
                        Forms\Components\Select::make('unit')
                            ->label('Satuan Produk')
                            ->required()
                            ->searchable()
                            ->options([
                                'Milligrams' => 'Milligrams (mg)',
                                'Grams' => 'Grams (g)',
                                'Kilograms' => 'Kilograms (kg)',
                                'Metric Tons' => 'Metric Tons (mt)',

                                'Millimeters' => 'Millimeters (mm)',
                                'Centimeters' => 'Centimeters (cm)',
                                'Meters' => 'Meters (m)',
                                'Kilometers' => 'Kilometers (km)',
                                'Inches' => 'Inches (in)',
                                'Feet' => 'Feet (ft)',
                                'Yards' => 'Yards (yd)',
                                'Miles' => 'Miles (mi)',

                                'Milliliters' => 'Milliliters (ml)',
                                'Liters' => 'Liters (l)',
                                'Cubic Centimeters' => 'Cubic Centimeters (cc)',
                                'Cubic Meters' => 'Cubic Meters (m³)',

                                'Pieces' => 'Pieces (pcs)',
                                'Dozens' => 'Dozens (dz)',
                                'Packs' => 'Packs (pack)',
                                'Cases' => 'Cases (case)',
                                'Cartons' => 'Cartons (ctn)',
                                'Pallets' => 'Pallets (plt)',

                                'Square Centimeters' => 'Square Centimeters (cm²)',
                                'Square Meters' => 'Square Meters (m²)',
                                'Square Kilometers' => 'Square Kilometers (km²)',
                                'Acres' => 'Acres (ac)',
                                'Hectares' => 'Hectares (ha)',

                                'Seconds' => 'Seconds (s)',
                                'Minutes' => 'Minutes (min)',
                                'Hours' => 'Hours (hr)',
                                'Days' => 'Days',
                                'Weeks' => 'Weeks',
                                'Months' => 'Months',
                                'Years' => 'Years',

                                'Joules' => 'Joules (J)',
                                'Kilojoules' => 'Kilojoules (kJ)',
                                'Calories' => 'Calories (cal)',
                                'Kilocalories' => 'Kilocalories (kcal)',

                                'Rolls' => 'Rolls',
                                'Tubes' => 'Tubes',
                                'Sheets' => 'Sheets',
                                'Bottles' => 'Bottles',
                                'Cans' => 'Cans',
                                'Bags' => 'Bags',
                                'Boxes' => 'Boxes',
                                'Bundles' => 'Bundles',
                            ])
                            ->helperText('Pilih satuan produk yang sesuai.'),
                    ])->columnSpan(1)->columns(2),

                Section::make('Deskripsi dan Gambar Produk')
                    ->collapsible()
                    ->description('Deskripsikan produk yang ingin ditambahkan.')
                    ->schema([
                        MarkdownEditor::make('description')->columnSpan('full')
                            ->label('Deskripsi Produk')
                            ->maxLength(255)
                            ->minLength(3)
                            ->helperText('Deskripsikan produk yang ingin ditambahkan.'),
                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar Produk')
                            ->disk('public')
                            ->directory('products')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->previewable()
                            ->helpertext('Sesuaikan gambar produk dengan nama produk.'),
                    ])
                    ->columnSpan(1),
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
                Tables\Columns\TextColumn::make('vendor.item') 
                    ->label('Vendor')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->description ?: 'Tidak ada deskripsi'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->formatStateUsing(function ($state) {
                        return 'Rp. ' . number_format($state, 0, ',', '.');
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->label('Satuan Produk')
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
                    ->label('Model Bisnis')
                    ->options(fn () => Category::pluck('name', 'id'))
                    ->placeholder('Semua Model Bisnis'),
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
