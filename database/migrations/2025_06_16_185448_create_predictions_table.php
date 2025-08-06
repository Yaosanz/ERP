<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->string('kategori');
            $table->bigInteger('pemasukan');
            $table->bigInteger('pengeluaran');
            $table->string('persentase_perubahan');
            $table->bigInteger('prediksi_keuntungan');
            $table->bigInteger('rata_rata_historis_kategori');
            $table->bigInteger('selisih');
            $table->string('status');
            $table->date('tanggal_prediksi');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
