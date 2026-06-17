<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            // ID utama dari database
            $table->uuid('id')->primary();
            
            // Seperti di Kotlin: "tabungan_123". Digunakan untuk mencegah duplikasi notifikasi.
            $table->string('reference_id')->unique(); 
            
            $table->string('title');
            $table->text('message');
            $table->string('type'); // Sesuai CSS Anda: 'success', 'danger', 'warning', 'info'
            $table->boolean('is_read')->default(false);
            
            // Menggantikan val timestamp: Long
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};