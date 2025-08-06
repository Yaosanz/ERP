<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionPayments;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;

class Prediction extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.prediction';
    protected static ?string $title = 'Prediksi Keuntungan';

    public string|int|null $category_id = null;
    public ?string $selectedCategoryName = null;
    public bool $hasPredicted = false;
    public array $predictionData = [];
    public array $predictionDetail = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('category_id')
                ->label('Model Bisnis')
                ->options(
                    ['all' => '📊 Semua Kategori'] + Category::pluck('name', 'id')->toArray()
                )
                ->searchable()
                ->required()
                ->placeholder('Pilih model bisnis'),
        ];
    }

    public function predict(): void
{
    $categories = Category::all();

    if ($categories->isEmpty()) {
        $this->predictionData = ['error' => 'Kategori tidak tersedia.'];
        $this->hasPredicted = true;
        return;
    }

    $today = now()->toDateString();

    $selectedCategories = [];

    // Cek jika semua atau satu kategori
    if ($this->category_id === 'all') {
        $selectedCategories = $categories;
    } else {
        $selected = $categories->firstWhere('id', $this->category_id);
        if ($selected) {
            $selectedCategories = collect([$selected]);
        }
    }

    $payload = [];

    foreach ($selectedCategories as $category) {
        $pemasukan = Transaction::incomes()
            ->where('category_id', $category->id)
            ->whereDate('date_transaction', $today)
            ->sum('amount');

        $pemasukan += TransactionPayments::incomes()
            ->where('category_id', $category->id)
            ->whereDate('date_transaction', $today)
            ->sum('amount');

        $pengeluaran = Transaction::expenses()
            ->where('category_id', $category->id)
            ->whereDate('date_transaction', $today)
            ->sum('amount');

        $pengeluaran += TransactionPayments::expenses()
            ->where('category_id', $category->id)
            ->whereDate('date_transaction', $today)
            ->sum('amount');

        if ($pemasukan == 0 && $pengeluaran == 0) {
            continue;
        }

        $payload[$category->name] = [
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'is_expense' => $category->is_expense ? 1 : 0,
        ];
    }

    if (empty($payload)) {
        $this->predictionData = ['error' => 'Tidak ada transaksi untuk kategori terpilih hari ini.'];
        $this->hasPredicted = true;
        return;
    }

    try {
        $response = Http::asJson()->post('http://127.0.0.1:5000/predict-total', $payload);

        if ($response->successful()) {
            $data = $response->json();

            $this->predictionData = [
                'tanggal_prediksi' => $data['tanggal_prediksi'],
                'total_prediksi_keuntungan' => $data['total_prediksi_keuntungan'],
            ];

            // FIX: simpan rincian per kategori
            if (isset($data['rincian_per_kategori'])) {
                $this->predictionData['rincian_per_kategori'] = $data['rincian_per_kategori'];
            }

            // Jika hanya satu kategori, simpan detail
            if ($this->category_id !== 'all' && count($data['rincian_per_kategori'] ?? []) === 1) {
                $rincian = $data['rincian_per_kategori'][0];
                $this->predictionData = array_merge($this->predictionData, $rincian);
            }

        } else {
            $this->predictionData = ['error' => 'Gagal mengakses API prediksi.'];
        }

    } catch (\Exception $e) {
        $this->predictionData = ['error' => 'Koneksi ke API gagal: ' . $e->getMessage()];
    }

    $this->hasPredicted = true;
}


}
