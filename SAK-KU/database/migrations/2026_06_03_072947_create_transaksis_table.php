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
    Schema::create('transaksis', function (Blueprint $table) {
        $table->id();
        // Menghubungkan transaksi ke ID Kantong tertentu
        $table->foreignId('kantong_id')->constrained('kantongs')->onDelete('cascade');
        $table->enum('jenis', ['masuk', 'keluar']); // Uang masuk atau keluar
        $table->bigInteger('nominal');
        $table->string('catatan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
