<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Product;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use App\Filament\Exports\TransactionExporter;
use App\Filament\Imports\TransactionImporter;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'carbon-product';
    protected static ?string $navigationGroup = "Transaksi";
    protected static ?string $navigationLabel = 'Pembayaran Produk';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Detail Transaksi')
                ->description('Isi form berikut untuk menambahkan transaksi baru.')
                ->aside()
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Transaksi')
                        ->placeholder('Masukkan nama transaksi')
                        ->required()
                        ->maxLength(50)
                        ->minLength(3),
                        
                    Select::make('category_id')
                        ->label('Model Bisnis')
                        ->placeholder('Pilih model bisnis')
                        ->relationship('category', 'name')
                        ->required()
                        ->preload()
                        ->helperText('Pilih Model Bisnis yang sesuai untuk transaksi ini.')
                        ->searchable(),

                    Select::make('product_id')
                        ->label('Produk')
                        ->placeholder('Pilih produk')
                        ->relationship('product', 'name')
                        ->reactive()
                        ->preload()
                        ->searchable()
                        ->helperText('Pilih produk yang dijual.')
                        ->afterStateUpdated(function (callable $set, $state) {
                            $product = Product:: find($state);
                            $set('price', $product?->price ?? 0); 
                            $set('quantity', 1); 
                            $set('amount', ($product?->price ?? 0) * 1);
                        }),

                    TextInput::make('product_name')
                        ->label('Label Produk')
                        ->placeholder('Masukkan label produk')
                        ->required()
                        ->maxLength(50)
                        ->minLength(3),

                    TextInput::make('price')
                        ->label('Harga Produk')
                        ->placeholder('Harga otomatis terisi dari produk')
                        ->reactive()
                        ->readonly()
                        ->prefix('Rp.'),

                    TextInput::make('quantity')
                        ->label('Kuantitas')
                        ->placeholder('Masukkan jumlah kuantitas')
                        ->numeric()
                        ->default(1)
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $price = $get('price');
                            $quantity = $get('quantity') ?? 1;
                            $set('amount', $price * $quantity);
                        }),

                    TextInput::make('amount')
                        ->label('Jumlah')
                        ->placeholder('Jumlah otomatis dihitung berdasarkan harga dan kuantitas')
                        ->prefix('Rp.')
                        ->numeric()
                        ->reactive()
                        ->required()
                        ->readonly(),
                    
                    Forms\Components\DatePicker::make('date_transaction')
                        ->label('Tanggal Pembayaran')
                        ->placeholder('Pilih tanggal pembayaran')
                        ->required(),
                ]),
            Section::make('Status Transaksi')
                ->collapsible()
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->placeholder('Pilih status transaksi')
                        ->required()
                        ->options([
                            'Paid' => 'Sudah Dibayar',
                            'Unpaid' => 'Belum Dibayar',
                            'Pending' => 'Menunggu',
                            'Cancelled' => 'Dibatalkan',
                        ]),
                    MarkdownEditor::make('description')
                        ->label('Deskripsi Transaksi')
                        ->placeholder('Tambahkan deskripsi transaksi')
                        ->maxLength(255)
                        ->minLength(3)
                        ->columnSpan('full'),
                ])
                ->columnSpan(1),
            Section::make('Bukti Transaksi')
                ->collapsible()
                ->schema([
                    Forms\Components\FileUpload::make('image')
                        ->label('Unggah Bukti')
                        ->placeholder('Unggah file gambar bukti transaksi')
                        ->disk('public')
                        ->directory('transactions')
                        ->image()
                        ->imageEditor()
                        ->downloadable()
                        ->previewable(),
                ])
                ->columnSpan(1),
        ])
        ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('category.image')
                        ->label('Model Bisnis'),
                Tables\Columns\IconColumn::make('category.is_expense')
                        ->label('Indikator')
                        ->boolean()
                        ->sortable()
                        ->toggleable()
                        ->searchable()
                        ->trueIcon('heroicon-o-arrow-up-circle')
                        ->falseIcon('heroicon-o-arrow-down-circle')
                        ->trueColor('danger')
                        ->falseColor('success'),
                TextColumn::make('category.name')
                        ->label('Model Bisnis')
                        ->sortable()
                        ->searchable(),
                TextColumn::make('name')
                        ->label('Nama Transaksi')
                        ->sortable()
                        ->toggleable()
                        ->searchable(),
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('product_name')
                    ->label('Label Produk')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Kuantitas')
                    ->numeric()
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('date_transaction')
                    ->label('Tanggal')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->toggleable()
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->description ?: 'Tidak ada deskripsi'),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->badge()
                    ->colors([
                        'success' => 'Paid',
                        'danger' => 'Unpaid',
                        'warning' => 'Pending',
                        'secondary' => 'Cancelled',
                    ])
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id')
                    ->formatStateUsing(function ($state) {
                        return 'Rp. ' . number_format($state, 0, ',', '.');
                    })
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Tanggal Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ])
                ->filters([
                   
                ])
                ->actions([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->headerActions([
                    ExportAction::make()->exporter(TransactionExporter::class)
                    ->label('Export Transaksi'),
                    ImportAction::make()->importer(TransactionImporter::class)
                    ->label('Import Transaksi')
                ])
                ->bulkActions([
                    Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ]),
                    ExportBulkAction::make()
                        ->exporter(TransactionExporter::class)
                        ->label('Export Transaksi'),
                ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
