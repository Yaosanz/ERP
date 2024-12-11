<?php

namespace App\Filament\Resources;
use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Product;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use App\Filament\Exports\TransactionExporter;
use App\Filament\Imports\TransactionImporter;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = "Transaksi";
    protected static ?string $navigationLabel = 'Pembayaran Produk';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Detail Transaksi')
                ->description('Isi form berikut untuk menambahkan transaksi baru.')
                ->collapsible()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Transaksi')
                        ->required()
                        ->maxLength(50)
                        ->minLength(3),
                        
                    Forms\Components\Select::make('category_id')
                        ->label('Kategori')
                        ->relationship('category', 'name'),

                    Forms\Components\Select::make('product_id')
                        ->label('Produk')
                        ->relationship('product', 'name')
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            $product = Product:: find($state);
                            $set('price', $product?->price ?? 0); 
                            $set('quantity', 1); 
                            $set('amount', ($product?->price ?? 0) * 1);
                        }),
                    Forms\Components\TextInput::make('product_name')
                        ->label('Varian Produk')
                        ->required()
                        ->maxLength(50)
                        ->minLength(3),

                    TextInput::make('price')
                        ->label('Harga Produk')
                        ->reactive() 
                        ->readonly()
                        ->prefix('Rp.'),

                    Forms\Components\TextInput::make('quantity')
                        ->label('Kuantitas')
                        ->numeric()
                        ->default(1)
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $price = $get('price');
                            $quantity = $get('quantity') ?? 1;
                            $set('amount', $price * $quantity);
                        }),

                    Forms\Components\TextInput::make('amount')
                        ->label('Jumlah')
                        ->prefix('Rp.')
                        ->numeric()
                        ->reactive()
                        ->required()
                        ->readonly(),
                    Forms\Components\DatePicker::make('date_transaction')
                        ->label('Tanggal Pembayaran')
                        ->required(),
                ])
                ->columnSpan(1)
                ->columns(2),
            Section::make('Status Transaksi')
                ->collapsible()
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->required()
                        ->options([
                            'Paid' => 'Paid',
                            'Unpaid' => 'Unpaid',
                            'Pending' => 'Pending',
                            'Cancelled' => 'Cancelled',
                        ]),
                    MarkdownEditor::make('description')->columnSpan('full')
                        ->label('Deskripsi Transaksi')
                        ->maxLength(255)
                        ->minLength(3),
                ])
                ->columnSpan(1),
            Section::make('Bukti Transaksi')
                ->collapsible()
                ->schema([
                    Forms\Components\FileUpload::make('image')
                        ->label('Unggah Bukti')
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
            Tables\Columns\ImageColumn::make('category.image')
                ->label('Kategori'),
                Tables\Columns\IconColumn::make('category.is_expense')
                ->label('Tipe')
                ->boolean()
                ->sortable()
                ->toggleable()
                ->searchable()
                ->trueIcon('heroicon-o-arrow-up-circle')
                ->falseIcon('heroicon-o-arrow-down-circle')
                ->trueColor('danger')
                ->falseColor('success'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Transaksi')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
            Tables\Columns\TextColumn::make('product.name')
                ->label('Produk')
                ->sortable()
                ->toggleable()
                ->searchable(),
            Tables\Columns\TextColumn::make('product_name')
                ->label('Varian Produk')
                ->sortable()
                ->toggleable()
                ->searchable(),
            Tables\Columns\TextColumn::make('quantity')
                ->label('Kuantitas')
                ->numeric()
                ->sortable()
                ->toggleable()
                ->searchable(),
            Tables\Columns\TextColumn::make('date_transaction')
                ->label('Tanggal')
                ->date()
                ->sortable()
                ->toggleable()
                ->searchable(),
            Tables\Columns\TextColumn::make('description')
                ->label('Deskripsi')
                ->searchable()
                ->toggleable()
                ->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->searchable()
                ->toggleable()
                ->sortable(),
            Tables\Columns\TextColumn::make('amount')
                ->label('Jumlah')
                ->money('IDR', locale: 'id')
                ->formatStateUsing(function ($state) {
                    return 'Rp. ' . number_format($state, 0, ',', '.');
                })
                ->sortable()
                ->toggleable()
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
