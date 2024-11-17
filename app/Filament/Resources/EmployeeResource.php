<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Departement;
use App\Models\Division;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'clarity-employee-group-line';
    protected static ?string $navigationGroup = "Manajemen";
    protected static ?string $navigationLabel = 'Kelola Karyawan';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Karyawan')
                    ->description('Isi detail informasi karyawan di bawah ini.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Karyawan')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique(Employee::class, 'email'),
                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'Male' => 'Male',
                                'Female' => 'Female',
                                'Other' => 'Other',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('position')
                            ->label('Posisi')
                            ->nullable(),
                        Forms\Components\Select::make('division')
                            ->label('Divisi')
                            ->options(Division::all()->pluck('division_name', 'id')->toArray()) 
                            ->nullable(),
                        
                        Forms\Components\Select::make('department_id')
                            ->label('Department')
                            ->relationship('department', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('salary')
                            ->label('Gaji')
                            ->numeric()
                            ->required(),
                        Forms\Components\DatePicker::make('hire_date')
                            ->label('Tanggal Bergabung')
                            ->nullable(),
                    ])->columns(2),  

                Section::make('Detail Alamat')
                    ->description('Isi detail alamat karyawan di bawah ini.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Alamat')
                            ->placeholder('Jl. Contoh No. 123 RT 01 RW 02 Kel. Contoh Kec. Contoh Kab. Contoh')
                            ->nullable(),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Kode Pos')
                            ->nullable(),
                        Forms\Components\TextInput::make('province')
                            ->label('Provinsi')
                            ->nullable(),
                        Forms\Components\TextInput::make('city')
                            ->label('Kota')
                            ->nullable(),
                        Forms\Components\TextInput::make('country')
                            ->label('Negara')
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Karyawan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Posisi')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('division')
                    ->label('Divisi')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('salary')
                    ->label('Gaji')
                    ->prefix('Rp.')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('hire_date')
                    ->label('Tanggal Bergabung')
                    ->dateTime()
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
