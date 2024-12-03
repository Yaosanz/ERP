<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'carbon-model-alt';
    protected static ?string $navigationGroup = "Model Bisnis";
    protected static ?string $navigationLabel = 'Kelola Bisnis Model';
    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make('Informasi Kategori')
                ->description('Isi informasi kategori kebutuhan berikut.')
                ->collapsible()
                ->schema([
                    Forms\Components\Select::make('name')
                        ->label('Kategori Kebutuhan')
                        ->required()
                        ->options([
                            'Konsumsi' => 'Konsumsi',
                            'Transportasi' => 'Transportasi',
                            'Jaringan Internet' => 'Jaringan Internet',
                            'Jasa' => 'Jasa',
                            'Penggandaan Barang' => 'Penggandaan Barang',
                            'Pemasangan' => 'Pemasangan',
                            'Penjualan Barang' => 'Penjualan Barang',
                            'Pemeliharaan' => 'Pemeliharaan',
                            'Pembelian' => 'Pembelian',
                            'Pengiriman' => 'Pengiriman',
                            'Gaji' => 'Gaji',
                            'Pembayaran' => 'Pembayaran',
                            'Penjualan' => 'Penjualan',
                            'Pengeluaran Lainnya' => 'Pengeluaran Lainnya',
                            'Pemasukan Lainnya' => 'Pemasukan Lainnya',
                        ]),
                ])
                ->columnSpan(1),

            Section::make('Bisnis Model')
                ->description('Matikan jika kategori ini adalah pemasukan Atau Nyalakan jika kategori ini adalah pengeluaran.')
                ->collapsible()
                ->schema([
                    Forms\Components\Toggle::make('is_expense')
                        ->label('Pemasukan Atau Pengeluaran')
                        ->default(false)
                        ->required(),
                ])
                ->columnSpan(1),

           
            Section::make('Logo Kategori')
                ->description('Cari logo sesuai dengan kebutuhan melalui situs yang bernama Flaticon atau Freepik.')
                ->collapsible()
                ->schema([
                    Forms\Components\FileUpload::make('image')
                        ->label('Logo Kategori')
                        ->image()
                ])
                ->columnSpan(1),
        ])
        ->columns(2);  
}



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                ->label('Logo'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Kategori Kebutuhan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_expense')
                    ->label('Tipe')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-up-circle')
                    ->falseIcon('heroicon-o-arrow-down-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Diubah')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Tanggal Dihapus')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
