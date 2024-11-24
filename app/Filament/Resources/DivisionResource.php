<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DivisionResource\Pages;
use App\Models\Division;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;


class DivisionResource extends Resource
{
    protected static ?string $navigationGroup = 'Manajemen'; 

    protected static ?string $navigationLabel = 'Kelola Divisi';

    protected static ?string $navigationParentItem = 'Kelola Departemen'; 

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('division_name')
                            ->label('Nama Divisi')
                            ->placeholder('Masukkan Nama Divisi')
                            ->required()
                            ->maxLength(255)
                            ->hint('Gunakan nama yang jelas dan mudah dipahami.')
                            ->autofocus()
                            ->helperText('Ex: IT, HR, Marketing, etc.'),

                        Forms\Components\Select::make('departments_id')
                            ->label('Departemen')
                            ->relationship('department', 'name')
                            ->placeholder('Pilih Departemen')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih departemen yang terkait dengan divisi ini.'),
                    ]),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->placeholder('Opsional : Masukkan deskripsi divisi')
                    ->rows(4)
                    ->columnSpan('full')
                    ->helperText('Gunakan deskripsi untuk memberikan informasi tambahan tentang divisi ini.'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('division_name')
                    ->label('Nama Divisi')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Departemen')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDivisions::route('/'),
            'create' => Pages\CreateDivision::route('/create'),
            'edit' => Pages\EditDivision::route('/{record}/edit'),
        ];
    }
}
