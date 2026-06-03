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
        Schema::create('kantongs', function (Blueprint $table) {
            $table->id(); // ID unik otomatis
            
            // Kolom yang kita butuhkan:
            $table->string('nama_kantong'); // Misal: "GoPay", "Beli Laptop"
            $table->enum('tipe', ['kantong', 'tabungan']); // Untuk membedakan jenisnya
            $table->bigInteger('saldo')->default(0); // Uang yang ada saat ini
            $table->bigInteger('target')->nullable(); // Target tabungan (boleh kosong untuk kantong biasa)
            
            $table->timestamps(); // Mencatat kapan dibuat & diupdate
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kantongs');
    }
};
