<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
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
                    Select::make('name')
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
                ->description('Tentukan apakah kategori kebutuhan ini merupakan pemasukan atau pengeluaran.')
                ->collapsible()
                ->schema([
                    ToggleButtons::make('is_expense')
                        ->label('Pemasukan Atau Pengeluaran')
                        ->default(false)
                        ->grouped()
                        ->options([
                            '0' => 'Pemasukan',
                            '1' => 'Pengeuaran',
                        ])
                        ->icons([
                            '0' => 'heroicon-o-arrow-trending-down',
                            '1' => 'heroicon-o-arrow-trending-up',
                        ])
                        ->colors([
                            '0' => 'success',
                            '1' => 'warning',
                        ])
                        ->required(),
                ])
                ->columnSpan(1),

           
            Section::make('Logo Kategori')
                ->description('Cari logo sesuai dengan kebutuhan melalui situs yang bernama Flaticon atau Freepik. (Opsional)')
                ->collapsible()
                ->schema([
                    FileUpload::make('image')
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
                ImageColumn::make('image')
                ->label('Logo'),
                TextColumn::make('name')
                    ->label('Kategori Kebutuhan')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_expense')
                    ->label('Bisnis Model')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-up-circle')
                    ->falseIcon('heroicon-o-arrow-down-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Tanggal Diubah')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
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
