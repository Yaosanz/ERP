<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Models\Vendor;
use App\Models\Provinces;
use App\Models\Cities;
use App\Models\Countries;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationIcon = 'clarity-employee-group-line';
    protected static ?string $navigationGroup = "Manajemen";
    protected static ?string $navigationLabel = 'Kelola Vendor';
    protected static ?string $navigationParentItem = 'Kelola Produk';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Vendor')
                    ->description('Isi detail informasi vendor di bawah ini.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Vendor')
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->nullable(),

                        Forms\Components\TextInput::make('number_phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->label('Status Vendor')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                            ])
                            ->required()
                            ->default('active'),
                    ])
                    ->columns(2),

                Section::make('Detail Alamat')
                    ->description('Isi detail alamat vendor di bawah ini.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Alamat')
                            ->placeholder('Alamat Lengkap')
                            ->nullable(),

                        Forms\Components\Select::make('country_id')
                            ->label('Negara')
                            ->relationship('country', 'name')
                            ->placeholder('Pilih Negara')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set) {
                                $set('province_id', null);  
                                $set('city_id', null);
                            }),

                        Forms\Components\Select::make('province_id')
                            ->label('Provinsi')
                            ->relationship('province', 'name')
                            ->placeholder('Pilih Provinsi')
                            ->reactive()
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function (callable $set) {
                                $set('city_id', null);  
                            })
                            ->options(function (callable $get) {
                                $countryId = $get('country_id');
                                if (!$countryId) {
                                    return [];
                                }
                                return Provinces::where('country_id', $countryId)
                                                ->pluck('name', 'id')
                                                ->toArray();
                            }),

                        Forms\Components\Select::make('city_id')
                            ->label('Kota')
                            ->relationship('city', 'name')
                            ->placeholder('Pilih Kota')
                            ->searchable()
                            ->preload()
                            ->options(function (callable $get) {
                                $provinceId = $get('province_id');
                                if (!$provinceId) {
                                    return [];
                                }
                                return Cities::where('province_id', $provinceId)
                                           ->pluck('name', 'id')
                                           ->toArray();
                            }),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Vendor')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('number_phone')
                    ->label('Nomor Telepon')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->label('Negara')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('province.name')
                    ->label('Provinsi')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Kota')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}