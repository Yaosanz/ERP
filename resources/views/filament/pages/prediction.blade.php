<x-filament::page>
    <div class="space-y-6">
        {{-- Formulir Kategori --}}
        <form wire:submit.prevent="predict">
            {{ $this->form }}
            <x-filament::button type="submit">
                🔮 Mulai Prediksi
            </x-filament::button>
        </form>

        {{-- Hasil Prediksi --}}
        @if ($hasPredicted)
            @if (isset($predictionData['error']))
                <div class="text-red-600 font-semibold mt-4">
                    {{ $predictionData['error'] }}
                </div>
            @else
                <div class="mt-6">
                    <table class="w-full text-sm border border-gray-300">
                        <tbody>
                            <tr>
                                <td class="border px-4 py-2 font-semibold">Tanggal Prediksi</td>
                                <td class="border px-4 py-2">{{ $predictionData['tanggal_prediksi'] ?? '-' }}</td>
                            </tr>

                            @if ($category_id !== 'all')
                                {{-- Jika 1 kategori --}}
                                <tr>
                                    <td class="border px-4 py-2 font-semibold">Kategori</td>
                                    <td class="border px-4 py-2">{{ $predictionData['kategori'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2 font-semibold">Pemasukan</td>
                                    <td class="border px-4 py-2">{{ $predictionData['pemasukan'] }}</td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2 font-semibold">Pengeluaran</td>
                                    <td class="border px-4 py-2">{{ $predictionData['pengeluaran'] }}</td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2 font-semibold">Prediksi Keuntungan</td>
                                    <td class="border px-4 py-2">{{ $predictionData['prediksi_keuntungan'] }}</td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2 font-semibold">Rata-rata Historis</td>
                                    <td class="border px-4 py-2">{{ $predictionData['rata_rata_historis_kategori'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2 font-semibold">Selisih</td>
                                    <td class="border px-4 py-2">{{ $predictionData['selisih'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2 font-semibold">Perubahan (%)</td>
                                    <td class="border px-4 py-2">{{ $predictionData['persentase_perubahan'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="border px-4 py-2 font-semibold">Status</td>
                                    <td class="border px-4 py-2">{{ ucfirst($predictionData['status'] ?? '-') }}</td>
                                </tr>
                            @endif

                            @if (isset($predictionData['total_prediksi_keuntungan']))
                                <tr>
                                    <td class="border px-4 py-2 font-semibold">Total Prediksi Keuntungan</td>
                                    <td class="border px-4 py-2">{{ $predictionData['total_prediksi_keuntungan'] }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Jika semua kategori, tampilkan rincian per kategori --}}
                @if ($category_id === 'all' && isset($predictionData['rincian_per_kategori']))
                    <h3 class="text-lg font-bold mt-6 mb-2">📊 Rincian Per Kategori</h3>
                    <table class="w-full text-sm border border-gray-300">
                        <thead>
                            <tr>
                                <th class="border px-4 py-2">Kategori</th>
                                <th class="border px-4 py-2">Pemasukan</th>
                                <th class="border px-4 py-2">Pengeluaran</th>
                                <th class="border px-4 py-2">Prediksi</th>
                                <th class="border px-4 py-2">Rata-rata Historis</th>
                                <th class="border px-4 py-2">Selisih</th>
                                <th class="border px-4 py-2">Perubahan</th>
                                <th class="border px-4 py-2">Status</th>
                                <th class="border px-4 py-2">Distribusi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($predictionData['rincian_per_kategori'] as $item)
                                <tr>
                                    <td class="border px-4 py-2">{{ $item['kategori'] }}</td>
                                    <td class="border px-4 py-2">{{ $item['pemasukan'] }}</td>
                                    <td class="border px-4 py-2">{{ $item['pengeluaran'] }}</td>
                                    <td class="border px-4 py-2">{{ $item['prediksi_keuntungan'] }}</td>
                                    <td class="border px-4 py-2">{{ $item['rata_rata_historis_kategori'] ?? '-' }}</td>
                                    <td class="border px-4 py-2">{{ $item['selisih'] ?? '-' }}</td>
                                    <td class="border px-4 py-2">{{ $item['persentase_perubahan'] ?? '-' }}</td>
                                    <td class="border px-4 py-2">{{ ucfirst($item['status'] ?? '-') }}</td>
                                    <td class="border px-4 py-2">{{ $item['posisi_prediksi_dalam_distribusi'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                {{-- Grafik jika hanya satu kategori --}}
                @if ($category_id !== 'all' && isset($predictionData['pemasukan'], $predictionData['pengeluaran'], $predictionData['prediksi_keuntungan']))
                    <div class="mt-8">
                        <canvas id="predictionChart" height="120"></canvas>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const ctx = document.getElementById("predictionChart").getContext("2d");
                            new Chart(ctx, {
                                type: "bar",
                                data: {
                                    labels: ["Pemasukan", "Pengeluaran", "Prediksi Keuntungan"],
                                    datasets: [{
                                        label: "Nilai (Rp)",
                                        data: [
                                            {{ (int) preg_replace('/\D/', '', $predictionData['pemasukan'] ?? '0') }},
                                            {{ (int) preg_replace('/\D/', '', $predictionData['pengeluaran'] ?? '0') }},
                                            {{ (int) preg_replace('/\D/', '', $predictionData['prediksi_keuntungan'] ?? '0') }}
                                        ],
                                        backgroundColor: [
                                            "rgba(59, 130, 246, 0.7)",
                                            "rgba(239, 68, 68, 0.7)",
                                            "rgba(34, 197, 94, 0.7)"
                                        ],
                                        borderColor: [
                                            "rgba(59, 130, 246, 1)",
                                            "rgba(239, 68, 68, 1)",
                                            "rgba(34, 197, 94, 1)"
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        });
                    </script>
                @endif
            @endif
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</x-filament::page>
