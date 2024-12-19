<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartemenResource\Pages;
use App\Models\Departement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DepartemenResource extends Resource
{
    protected static ?string $model = Departement::class;

    protected static ?string $navigationIcon = 'clarity-organization-line';
    protected static ?string $navigationGroup = "Manajemen";
    protected static ?string $navigationLabel = 'Kelola Departemen';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\Section::make('Informasi Departemen')
                        ->description('Silahkan isi form berikut untuk menambahkan departemen baru.')
                        ->collapsible()
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Departemen')
                                ->maxLength(50)
                                ->minLength(3)
                                ->required()
                                ->helperText('Misalnya: Sales & Marketing, Human Resources (HR), Purchasing, Production, Accounting, Information & Technology (IT), Finance, Customer Support, Legal, Research & Development (R&D), Operations, Supply Chain, Logistics')
                                ->placeholder('Masukkan Nama Departemen'),

                            Forms\Components\Textarea::make('description')
                                ->label('Deskripsi Departemen')
                                ->maxLength(255)
                                ->nullable()
                                ->helperText('Jelaskan secara singkat tentang departemen ini terkait tugas dan tanggung jawabnya.')
                                ->placeholder('Masukkan deskripsi departemen'),
                        ])
                        ->columnSpan(1) 
                        ->columns(1),

                    // Column 2: Divisions Info
                    // Forms\Components\Section::make('Divisi')
                    //     ->description('Tambahkan divisi terkait departemen ini.')
                    //     ->collapsible()
                    //     ->schema([
                    //         Forms\Components\Repeater::make('divisions')
                    //             ->label('Divisi')
                    //             ->schema([
                    //                 Forms\Components\TextInput::make('division_name')
                    //                     ->label('Nama Divisi')
                    //                     ->maxLength(100)
                    //                     ->nullable()
                    //                     ->placeholder('Pemasaran Digital')
                    //                     ->helperText('Misalnya: Divisi Penjualan (Sales), Keamanan Siber (Cyber Security), Pemasaran Digital (Digital Marketing), Pengembangan Bisnis (Business Development), Keuangan (Finance), Sumber Daya Manusia (HRD), Produksi (Production), Pengadaan (Procurement), Operasional (Operations), Perencanaan Strategis (Strategic Planning), dan lain-lain.')
                    //             ])
                    //             ->minItems(1)
                    //             ->maxItems(5)
                    //             ->helperText('Tambahkan divisi terkait departemen ini.')
                    //             ->addActionLabel('Tambah Divisi Baru')
                    //             ->collapsed(),
                    //     ])
                    //     ->columnSpan(1) // Span only 1 column
                    //     ->columns(1),
                ]),
        ])
        ->columns(1); // Specify that the form should use a two-column layout
}

    /**
     * Method to handle post-save actions.
     */
    public static function afterSave($record, array $data): void
    {
        // Get divisions data from the form
        $divisions = $data['divisions'] ?? [];
    
        // Save divisions if any are provided
        if (!empty($divisions)) {
            $record->saveDivisions($divisions);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Departemen')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi Departemen')
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
        return [
            // Add relations if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartemens::route('/'),
            'create' => Pages\CreateDepartemen::route('/create'),
            'edit' => Pages\EditDepartemen::route('/{record}/edit'),
        ];
    }
}

