<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionsIncomesResource\Pages;
use App\Models\TransactionsIncomes;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class TransactionsIncomesResource extends Resource
{
    protected static ?string $model = TransactionsIncomes::class;

    protected static ?string $navigationIcon = 'iconpark-incomeone';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Transaksi Pemasukan';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Transaksi')
                    ->description('Isi form berikut untuk menambahkan pemasukan baru.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Transaksi')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Masukkan nama untuk transaksi pemasukan.'),
                        Select::make('category_id')
                            ->label('Model Bisnis')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->helperText('Pilih Model Bisnis yang sesuai untuk transaksi ini.'),
                        DatePicker::make('date_transaction')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->helperText('Pilih tanggal terjadinya transaksi.'),
                        TextInput::make('amount')
                            ->label('Jumlah')
                            ->prefix('Rp.')
                            ->numeric()
                            ->required()
                            ->helperText('Masukkan jumlah uang yang diterima.')
                    ])
                    ->columns(2),
                Section::make('Status Transaksi')
                    ->collapsible()
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'Paid' => 'Paid',
                                'Unpaid' => 'Unpaid',
                                'Pending' => 'Pending',
                            ])
                            ->helperText('Pilih status transaksi.'),
                        MarkdownEditor::make('description')
                            ->label('Deskripsi')
                            ->maxLength(500)
                            ->helperText('Deskripsikan lebih lanjut transaksi pemasukan ini.')
                    ]),
                Section::make('Bukti Transaksi')
                    ->collapsible()
                    ->schema([
                        FileUpload::make('image')
                            ->label('Unggah Bukti')
                            ->disk('public')
                            ->directory('transactions_incomes')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->helperText('Unggah bukti transaksi berupa gambar atau dokumen.')
                    ]),
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
                    ->searchable(),
                TextColumn::make('date_transaction')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->description ?: 'Tidak ada deskripsi'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Paid' => 'Paid',
                        'Unpaid' => 'Unpaid',
                        'Pending' => 'Pending',
                    ]),
                Filter::make('date_transaction')
                    ->label('Tanggal Transaksi')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Mulai'),
                        DatePicker::make('end_date')
                            ->label('Selesai'),
                    ])
                    ->query(function ($query, $data) {
                        return $query
                            ->when($data['start_date'], fn ($q, $start) => $q->whereDate('date_transaction', '>=', $start))
                            ->when($data['end_date'], fn ($q, $end) => $q->whereDate('date_transaction', '<=', $end));
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactionsIncomes::route('/'),
            'create' => Pages\CreateTransactionsIncomes::route('/create'),
            'edit' => Pages\EditTransactionsIncomes::route('/{record}/edit'),
        ];
    }
}
