<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('address')->nullable();
            $table->foreignId('province_id')->nullable()->constrained('provinces')->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained('countries')->cascadeOnDelete();
            $table->string('postal_code')->nullable();
            $table->string('position')->nullable();
            $table->foreignId('divisions_id')->nullable()->constrained('divisions')->cascadeOnDelete();
            $table->integer('salary');
            $table->date('hire_date')->nullable();
            $table->foreignId('departments_id')->nullable()->constrained('departments')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};