<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionPaymentsResource\Pages;
use App\Models\TransactionPayments;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class TransactionPaymentsResource extends Resource
{
    protected static ?string $model = TransactionPayments::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Transaksi General';

    public static function form(Forms\Form $form): Forms\Form
{
    return $form
        ->schema([
            Section::make('Detail Transaksi')
                ->description('Isi formulir berikut untuk menambahkan transaksi baru.')
                ->aside()
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Transaksi')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Masukkan nama transaksi')
                        ->helperText('Nama transaksi maksimal 255 karakter.')
                        ->columnSpanFull(),

                    Select::make('category_id')
                        ->label('Bisnis Model')
                        ->relationship('category', 'name')
                        ->required()
                        ->preload()
                        ->searchable()
                        ->placeholder('Pilih kategori transaksi'),

                    TextInput::make('amount')
                        ->label('Jumlah')
                        ->numeric()
                        ->maxLength(20)
                        ->prefix('Rp')
                        ->required()
                        ->placeholder('Masukkan jumlah transaksi')
                        ->helperText('Pastikan jumlah sesuai dengan nilai transaksi.'),

                    DatePicker::make('date_transaction')
                        ->label('Tanggal Transaksi')
                        ->required()
                        ->placeholder('Pilih tanggal transaksi'),

                    TextInput::make('quantity')
                        ->label('Kuantitas')
                        ->numeric()
                        ->maxLength(5)
                        ->placeholder('Masukkan jumlah barang')
                        ->helperText('(Opsional) Jika ini transaksi barang atau pengiriman, masukkan jumlah barang.'),
                ]),

            Section::make('Detail Kendaraan')
                ->collapsed()
                ->schema([
                    FileUpload::make('vehicle_image')
                        ->label('Gambar Kendaraan')
                        ->image()
                        ->directory('kendaraan-images')
                        ->maxSize(1024)
                        ->helperText('Unggah gambar kendaraan terkait transaksi, maksimal 1MB.'),

                    TextInput::make('vehicle_plate')
                        ->label('Plat Nomor Kendaraan')
                        ->maxLength(10)
                        ->placeholder('Masukkan plat nomor kendaraan'),

                    TextInput::make('region')
                        ->label('Wilayah')
                        ->maxLength(30)
                        ->placeholder('Masukkan wilayah operasional kendaraan'),
                ]),

            Section::make('Status Transaksi')
                ->collapsible()
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'Paid' => 'Sudah Dibayar',
                            'Unpaid' => 'Belum Dibayar',
                            'Pending' => 'Menunggu Konfirmasi',
                        ])
                        ->default('Unpaid')
                        ->required()
                        ->helperText('Pilih status transaksi.'),

                    MarkdownEditor::make('description')
                        ->label('Deskripsi')
                        ->maxLength(255)
                        ->minLength(3)
                        ->placeholder('Masukkan deskripsi transaksi')
                        ->helperText('Berikan deskripsi singkat terkait transaksi.'),
                ]),

            Section::make('Bukti Transaksi')
                ->collapsible()
                ->schema([
                    FileUpload::make('image')
                        ->label('Unggah Bukti/Foto')
                        ->image()
                        ->directory('bukti-transaksi')
                        ->maxSize(1024) 
                        ->helperText('Unggah file dalam format gambar, maksimal 1MB.'),
                ]),
        ])
        ->columns(2);
}

public static function table(Tables\Table $table): Tables\Table
{
    return $table
        ->columns([
            ImageColumn::make('category.image')
                ->label('Logo'),
            IconColumn::make('category.is_expense')
                ->label('Indikator')
                ->boolean()
                ->sortable()
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
                ->searchable()
                ->sortable(),

            TextColumn::make('amount')
                ->label('Jumlah')
                ->money('IDR', true)
                ->sortable()
                ->formatStateUsing(function ($state) {
                    return 'Rp. ' . number_format($state, 0, ',', '.');
                }),

            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->colors([
                    'success' => 'Paid',
                    'danger' => 'Unpaid',
                    'warning' => 'Pending',
                ])
                ->sortable(),
            TextColumn::make('date_transaction')
                ->label('Tanggal Transaksi')
                ->date('d M Y')
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Dibuat Pada')
                ->dateTime('d M Y, H:i')
                ->sortable(),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')
                ->label('Status')
                ->options([
                    'Paid' => 'Sudah Dibayar',
                    'Unpaid' => 'Belum Dibayar',
                    'Pending' => 'Menunggu Konfirmasi',
                ]),

            Tables\Filters\Filter::make('date_transaction')
                ->label('Tanggal Transaksi')
                ->form([
                    DatePicker::make('from')->label('Dari Tanggal'),
                    DatePicker::make('to')->label('Hingga Tanggal'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when($data['from'], fn (Builder $query, $date) => $query->where('date_transaction', '>=', $date))
                        ->when($data['to'], fn (Builder $query, $date) => $query->where('date_transaction', '<=', $date));
                }),
        ])
        ->actions([
            ViewAction::make(),
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
        'index' => Pages\ListTransactionPayments::route('/'),
        'create' => Pages\CreateTransactionPayments::route('/create'),
        'view' => Pages\ViewTransactionPayments::route('/{record}'),
        'edit' => Pages\EditTransactionPayments::route('/{record}/edit'),
    ];
}
}
