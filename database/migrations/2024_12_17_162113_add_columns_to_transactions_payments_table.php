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
        Schema::table('transactions_payments', function (Blueprint $table) {
            $table->integer('quantity')->nullable()->after('amount'); // Kolom kuantitas item
            $table->string('vehicle_image')->nullable()->after('image'); // Kolom gambar kendaraan
            $table->string('vehicle_plate')->nullable()->after('vehicle_image'); // Kolom plat nomor kendaraan
            $table->string('region')->nullable()->after('vehicle_plate'); // Kolom wilayah atau area pengiriman
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions_payments', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'vehicle_image', 'vehicle_plate', 'region']);
        });
    }
};
