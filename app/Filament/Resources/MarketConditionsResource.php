<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarketConditionsResource\Pages;
use App\Filament\Resources\MarketConditionsResource\RelationManagers;
use App\Models\MarketConditions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MarketConditionsResource extends Resource
{
    protected static ?string $model = MarketConditions::class;

    protected static ?string $navigationIcon = 'carbon-chart-3d';
    protected static ?string $navigationGroup = "Model Bisnis";
    protected static ?string $navigationLabel = 'Kelola Kondisi Pasar';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Kondisi Pasar')
                    ->description('Isi informasi kondisi pasar berikut.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->required(),
                        Forms\Components\TextInput::make('economic_indicator')
                            ->label('Indikator Ekonomi')
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('market_trend')
                            ->label('Tren Pasar')
                            ->required()
                            ->options([
                                'Naik' => 'Naik',
                                'Turun' => 'Turun',
                                'Stabil' => 'Stabil',
                            ]),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('economic_indicator')
                    ->label('Indikator Ekonomi')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('market_trend')
                    ->label('Tren Pasar')
                    ->sortable()
                    ->searchable(),
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
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMarketConditions::route('/'),
            'create' => Pages\CreateMarketConditions::route('/create'),
            'edit' => Pages\EditMarketConditions::route('/{record}/edit'),
        ];
    }
}
