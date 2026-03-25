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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama barang (e.g., "Infokus Epson EB-X05")
            $table->string('category'); // Kategori: elektronik, transportasi, audio, dll
            $table->decimal('price_per_day', 12, 2)->default(0); // Harga sewa per hari (0 = gratis)
            $table->integer('stock')->default(1); // Total stok
            $table->integer('stock_available')->default(1); // Stok tersedia
            $table->boolean('is_active')->default(true); // Aktif/tidak
            $table->string('image')->nullable(); // Foto barang
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
