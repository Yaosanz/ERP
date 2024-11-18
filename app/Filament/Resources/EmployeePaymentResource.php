<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeePaymentResource\Pages;
use App\Models\Employee;
use App\Models\EmployeePayment;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Component;

class EmployeePaymentResource extends Resource
{
    protected static ?string $model = EmployeePayment::class;

    protected static ?string $navigationIcon = 'clarity-employee-line';
    protected static ?string $navigationGroup = "Transaksi";
    protected static ?string $navigationLabel = 'Gaji Karyawan';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Pembayaran Karyawan')
                    ->description('Silahkan isi form berikut untuk menambahkan pembayaran karyawan.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Nama Karyawan')
                            ->relationship('employee', 'name')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $employee = Employee::find($state);
                                    $set('amount', $employee ? $employee->salary : 0);
                                }
                            }),
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Pembayaran')
                            ->numeric()
                            ->disabled()
                            ->prefix('Rp.')
                            ->required(),
                        Forms\Components\DatePicker::make('payment_date')
                            ->label('Tanggal Pembayaran')
                            ->required(),
                        Forms\Components\Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->required()
                            ->options([
                                'Cash on Hand' => 'Cash on Hand',
                                'Bank Transfer' => 'Bank Transfer',
                                'Mobile Payment' => 'Mobile Payment',
                            ]),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'Paid' => 'Paid',
                                'Unpaid' => 'Unpaid',
                                'Pending' => 'Pending',
                                'Cancelled' => 'Cancelled',
                            ])
                    ])->columnSpan(1)->columns(2),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Nama Karyawan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah Pembayaran')
                    ->prefix('Rp.')
                    ->formatStateUsing(function ($state) {
                        return 'Rp. ' . number_format($state, 0, ',', '.');
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Tanggal Pembayaran')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Pembayaran')
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
            'index' => Pages\ListEmployeePayments::route('/'),
            'create' => Pages\CreateEmployeePayment::route('/create'),
            'edit' => Pages\EditEmployeePayment::route('/{record}/edit'),
        ];
    }
}
