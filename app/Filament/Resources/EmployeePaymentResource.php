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


class EmployeePaymentResource extends Resource
{
    protected static ?string $model = EmployeePayment::class;

    protected static ?string $navigationIcon = 'tabler-user-dollar';
    protected static ?string $navigationGroup = "Transaksi";
    protected static ?string $navigationLabel = 'Pembayaran Gaji';
    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Pembayaran Karyawan')
                    ->description('Silahkan isi form berikut untuk menambahkan pembayaran karyawan.')
                    ->aside()
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label('Nama Karyawan')
                            ->placeholder('Pilih nama karyawan')
                            ->helperText('Pilih karyawan yang akan menerima pembayaran.')
                            ->relationship('employee', 'name') // Menghubungkan dengan model Employee
                            ->required()
                            ->reactive()
                            ->searchable()
                            ->preload()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $employee = Employee::find($state);
                                    $set('amount', $employee ? $employee->salary : 0);
                                }
                            }),
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Pembayaran')
                            ->placeholder('Masukkan jumlah pembayaran')
                            ->helperText('Jumlah pembayaran akan otomatis diisi berdasarkan gaji karyawan.')
                            ->numeric()
                            ->prefix('Rp.')
                            ->readonly()
                            ->required(),
                        Forms\Components\DatePicker::make('payment_date')
                            ->label('Tanggal Pembayaran')
                            ->placeholder('Pilih tanggal pembayaran')
                            ->helperText('Tanggal pembayaran yang dilakukan.')
                            ->required(),
                        Forms\Components\Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->placeholder('Pilih metode pembayaran')
                            ->helperText('Pilih metode pembayaran yang digunakan.')
                            ->required()
                            ->options([
                                'Cash on Hand' => 'Tunai',
                                'Bank Transfer' => 'Transfer Bank',
                                'Mobile Payment' => 'Pembayaran Digital',
                            ]),
                        Forms\Components\Select::make('status')
                            ->label('Status Pembayaran')
                            ->placeholder('Pilih status pembayaran')
                            ->helperText('Tentukan status pembayaran saat ini.')
                            ->required()
                            ->options([
                                'Paid' => 'Sudah Dibayar',
                                'Unpaid' => 'Belum Dibayar',
                                'Pending' => 'Menunggu',
                                'Cancelled' => 'Dibatalkan',
                            ]),
                    ]),

                Section::make('Bukti Pembayaran')
                    ->description('Unggah gambar sebagai bukti pembayaran transaksi.')
                    ->collapsed()
                    ->schema([
                        Forms\Components\FileUpload::make('proof_of_payment')
                            ->label('Unggah Bukti Pembayaran')
                            ->helperText('Unggah gambar sebagai bukti bahwa pembayaran telah dilakukan.')
                            ->image()
                            ->directory('payments/proofs')
                    ]),
            ]);
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
                    ->badge()
                    ->color(fn ($record) => match ($record->status) {
                        'Paid' => 'success',
                        'Unpaid' => 'danger',
                        'Pending' => 'warning',
                        'Cancelled' => 'danger',
                        default => 'primary',
                    })
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
