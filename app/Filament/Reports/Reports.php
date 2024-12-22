<?php

namespace App\Filament\Reports;

use App\Models\Transaction;
use App\Models\EmployeePayment;
use App\Models\TransactionPayments;
use EightyNine\Reports\Components\Footer\Layout\FooterRow;
use EightyNine\Reports\Components\Image;
use EightyNine\Reports\Components\Text;
use EightyNine\Reports\Components\VerticalSpace;
use EightyNine\Reports\Report;
use EightyNine\Reports\Components\Body;
use EightyNine\Reports\Components\Footer;
use EightyNine\Reports\Components\Header;
use Filament\Forms\Form;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Carbon\Carbon;

class Reports extends Report
{
    protected static ?string $navigationGroup = "Laporan Keuangan";
    protected static ?string $navigationLabel = 'Rekapitulasi Transaksi';

    public function header(Header $header): Header
{
    $imagePath = asset('images/logo.jpg');

    return $header
        ->schema([
            Header\Layout\HeaderRow::make()
                ->schema([
                    Header\Layout\HeaderColumn::make()
                        ->schema([
                            Image::make($imagePath)
                                ->width9Xl(),
                        ])->alignLeft(),
                    Header\Layout\HeaderColumn::make()
                        ->schema([
                            VerticalSpace::make(),
                            Text::make("CV. Surya Cipta Estetika Mandiri")
                                ->title()
                                ->primary(),
                            Text::make("Laporan Keuangan")
                                ->subtitle(),
                            Text::make("Rekapitulasi Transaksi")
                                ->subtitle(),
                            Text::make("Periode: " . Carbon::now()->startOfYear()->format('F Y') . " - " . Carbon::now()->format('F Y'))
                                ->subtitle(),
                        ])->alignRight(),
                ]),
        ]);
}
    public function body(Body $body): Body
{
    return $body
        ->schema([
            Body\Layout\BodyColumn::make()
                ->schema([
                    Text::make("Rekapitulasi Penggandaan Barang")
                        ->fontXl()
                        ->fontBold()
                        ->primary(),
                    Text::make("Berikut adalah rekapitulasi data transaksi yang terjadi pada periode " . Carbon::now()->startOfYear()->format('F Y') . " - " . Carbon::now()->format('F Y'))
                        ->fontSm()
                        ->secondary(),
                    Body\Table::make()
                        ->columns([
                            Body\TextColumn::make("name")
                                ->label("Transaksi")
                                ->alignLeft(),
                            Body\TextColumn::make("product_name")
                                ->label("Produk")
                                ->alignLeft(),
                            Body\TextColumn::make("quantity")
                                ->label("Kuantitas")
                                ->alignCenter(),
                            Body\TextColumn::make("amount")
                                ->label("Jumlah")
                                ->alignCenter()
                                ->formatStateUsing(function ($state) {
                                    return 'Rp. ' . number_format($state, 0, ',', '.');
                                }),
                            Body\TextColumn::make("date_transaction")
                                ->label("Tanggal")
                                ->dateTime()
                                ->alignCenter(),
                        ])
                        ->data(
                            function (?array $filters) {
                                [$from, $to] = $this->getCarbonInstancesFromDateString(
                                    $filters['transaction_date'] ?? null
                                );
                                return collect(Transaction::query()
                                    ->when($from, function ($query, $date) {
                                        return $query->whereDate('date_transaction', '>=', $date);
                                    })
                                    ->when($to, function ($query, $date) {
                                        return $query->whereDate('date_transaction', '<=', $date);
                                    })
                                    ->select("name","product_name", "quantity", "amount", "date_transaction")
                                    ->take(10)
                                    ->get());
                            }
                        ),
                        VerticalSpace::make(),
                    Text::make("Rekapitulasi Transaksi General")
                        ->fontXl()
                        ->fontBold()
                        ->primary(),
                    Text::make("Berikut adalah rekapitulasi data transaksi yang terjadi pada periode " . Carbon::now()->startOfYear()->format('F Y') . " - " . Carbon::now()->format('F Y'))
                        ->fontSm()
                        ->secondary(),
                        Body\Table::make()
                        ->columns([
                            Body\TextColumn::make("name")
                                ->label("Transaksi")
                                ->alignLeft(),
                            Body\TextColumn::make("amount")
                                ->label("Jumlah")
                                ->alignCenter()
                                ->formatStateUsing(function ($state) {
                                    return 'Rp. ' . number_format($state, 0, ',', '.');
                                }),
                            Body\TextColumn::make("date_transaction")
                                ->label("Tanggal")
                                ->dateTime()
                                ->alignCenter(),
                        ])
                        ->data(
                            function (?array $filters) {
                                // Extract date range from filters if available
                                [$from, $to] = $this->getCarbonInstancesFromDateString(
                                    $filters['transaction_date'] ?? null
                                );
                    
                                // Check if the filter for 'is_expense' is provided
                                $isExpense = $filters['is_expense'] ?? null; // null means no filter, you can modify this if you need a default value
                                
                                // Initialize the query
                                $query = TransactionPayments::query()
                                    ->when($from, function ($query, $date) {
                                        return $query->whereDate('date_transaction', '>=', $date);
                                    })
                                    ->when($to, function ($query, $date) {
                                        return $query->whereDate('date_transaction', '<=', $date);
                                    })
                                    ->with('category') 
                                    ->select('name', 'amount', 'date_transaction');
                    
                                // Apply expense or income filter if present
                                if ($isExpense !== null) {
                                    if ($isExpense) {
                                        $query->expenses(); // Apply the expenses scope
                                    } else {
                                        $query->incomes(); // Apply the incomes scope
                                    }
                                }
                    
                                // Fetch the data and map it to the required structure
                                return collect($query->get())->map(function ($payment) {
                                    return [
                                        'name' => $payment->name,
                                        'quantity' => $payment->quantity,
                                        'amount' => $payment->amount,
                                        'date_transaction' => $payment->date_transaction,
                                    ];
                                });
                            }
                        )
                    ,
                        VerticalSpace::make(),
                        Text::make("Rekapitulasi Gaji Karyawan")
                        ->fontXl()
                        ->fontBold()
                        ->primary(),
                        Body\Table::make()
                        ->columns([
                        Body\TextColumn::make("employee_name")
                            ->label("Nama Karyawan")
                            ->alignLeft(),
                        Body\TextColumn::make("amount")
                            ->label("Jumlah")
                            ->numeric()
                            ->alignCenter()
                            ->formatStateUsing(function ($state) {
                                return 'Rp. ' . number_format($state, 0, ',', '.');
                            }),
                        Body\TextColumn::make("payment_date")
                            ->label("Tanggal Pembayaran")
                            ->dateTime()
                            ->alignRight(),
                ])
                ->data(
                    function (?array $filters) {
                        [$from, $to] = $this->getCarbonInstancesFromDateString(
                            $filters['transaction_date'] ?? null
                        );
                        return collect(EmployeePayment::query()
                            ->whereBetween('payment_date', [$from, $to])
                            ->with('employee')
                            ->get())
                            ->map(function ($payment) {
                                return [
                                    'employee_name' => $payment->employee->name ?? 'Tidak Diketahui',
                                    'amount' => $payment->amount,
                                    'payment_date' => $payment->payment_date,
                                ];
                            });
                    }
                ),
                VerticalSpace::make(),
                Text::make("Statistik Keuangan Perusahaan")
                ->fontXl()
                ->fontBold()
                ->primary(),

            Body\Table::make()
                ->columns([
                    Body\TextColumn::make("item")
                        ->label("Kategori")
                        ->alignLeft(),
                    Body\TextColumn::make("total")
                        ->label("Total")
                        ->numeric()
                        ->alignRight(),
                ])
                ->data(
                    function (?array $filters) {
                        $dates = $this->getCarbonInstancesFromDateString($filters['transaction_date'] ?? null);

                        $incomes = Transaction::query()
                        ->whereBetween('date_transaction', [$dates[0], $dates[1]])
                        ->whereHas('category', function ($query) {
                            $query->where('is_expense', false); 
                        })
                        ->sum('amount');
                        $otherincomes = TransactionPayments::query()
                        ->whereBetween('date_transaction', [$dates[0], $dates[1]])
                        ->whereHas('category', function ($query) {
                            $query->where('is_expense', false); 
                        })
                        ->sum('amount');

                        $expenses = TransactionPayments::query()
                        ->whereBetween('date_transaction', [$dates[0], $dates[1]])
                        ->whereHas('category', function ($query) {
                            $query->where('is_expense', true); 
                        })
                        ->sum('amount');

                        // Tambahkan total gaji karyawan sebagai bagian dari pengeluaran
                        $employeePayments = EmployeePayment::where('status', 'Paid')
                        ->whereBetween('payment_date', [$dates[0], $dates[1]])
                        ->sum('amount');

                        // Total pengeluaran adalah jumlah dari transaksi pengeluaran dan gaji karyawan
                        $totalExpenses = $expenses + $employeePayments;

                        // Total pendapatan adalah jumlah dari transaksi pengeluaran dan gaji karyawan
                        $totalIncomes = $otherincomes + $incomes;

                        // Hitung keuntungan
                        $profit = $totalIncomes - $totalExpenses;

                        return collect([
                        ["item" => "Pendapatan", "total" => "Rp. " . number_format($totalIncomes, 0, ',', '.')], 
                        ["item" => "Pengeluaran", "total" => "Rp. " . number_format($totalExpenses, 0, ',', '.')], 
                        ["item" => "Keuntungan", "total" => "Rp. " . number_format($profit, 0, ',', '.')], 
                        ]);

                    }
                ),
                ])
        ]);
}


public function filterForm(Form $form): Form
{
    return $form
    ->schema([
        DateRangePicker::make("transaction_date")
        ->label("Tanggal Transaksi")
        ->placeholder("Tentukan Tanggal Transaksi"),
    ]);
}
public function footer(Footer $footer): Footer
{
    return $footer->schema([
        FooterRow::make()->schema([
            Text::make("Dicetak Pada Tanggal: " . Carbon::now()->format('d F Y H:i:s A'))
                ->fontSm(), 
        ]),  
    ]);
}

    private function getCarbonInstancesFromDateString(?string $dateRange): array
{
    if (!$dateRange) {
        // If no date range is provided, return the current year start and today's date
        return [
            Carbon::now()->startOfYear(), 
            Carbon::now()->endOfYear(),
        ];
    }

    // If a date range is provided, split it into start and end
    [$start, $end] = explode(' - ', $dateRange);

    // Return the parsed Carbon instances
    return [
        Carbon::createFromFormat('d/m/Y', trim($start)), 
        Carbon::createFromFormat('d/m/Y', trim($end))
    ];
}

}
