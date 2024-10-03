<?php

namespace App\Filament\Resources;
use App\Exports\TransactionExport;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Collection;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ExportAction as ActionsExportAction;
use App\Filament\Exports\TransactionExporter;
use App\Filament\Imports\TransactionImporter;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Filament\Tables\Filters\DateFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ImportAction;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'carbon-currency';
    protected static ?string $navigationGroup = "Pembayaran";
    protected static ?int $navigationSort = 3;

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
                            ->relationship('product', 'name'),
                        Forms\Components\DatePicker::make('date_transaction')
                            ->label('Tanggal Transaksi')
                            ->required(),
                        Forms\Components\TextInput::make('product_name')
                            ->label('Nama Varian Produk')
                            ->maxLength(50) 
                            ->minLength(3),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Kuantitas')
                            ->maxLength(7) 
                            ->minLength(1)
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah')
                            ->prefix('Rp.')
                            ->maxLength(10) 
                            ->minLength(3)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'Paid' => 'Paid',
                                'Unpaid' => 'Unpaid',
                                'Pending' => 'Pending',
                                'Cancelled' => 'Cancelled',
                            ])
                            ->default('Belum Dibayar'),
                        MarkdownEditor::make('description')->columnSpan('full')
                            ->label('Deskripsi Transaksi')
                            ->maxLength(255) 
                            ->minLength(3)
                    ])
                    ->columnSpan(1)
                    ->columns(2),
                Section::make('Bukti Transaksi')
                    ->collapsible()
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Unggah Bukti')
                            ->image()
                            ->visibility('private'),
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
                Tables\Columns\TextColumn::make('category.name')
                ->description(fn (Transaction $record): string => $record->name)
                ->label('Nama Transaksi')
                ->sortable()
                ->toggleable()
                ->searchable(),
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
                    ->money('IDR', locale:'id')
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
