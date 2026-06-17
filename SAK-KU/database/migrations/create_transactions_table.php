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
        Schema::create('transactions', function (Blueprint $table) {
            // val id: String (UUID)
            $table->uuid('id')->primary();
            
            // val keterangan: String
            $table->string('keterangan');
            
            // val nominal: Double
            $table->double('nominal', 15, 2);
            
            // val isPemasukan: Boolean
            $table->boolean('is_pemasukan');
            
            // val kategori: String
            $table->string('kategori');
            
            // val alokasiId: String? (Nullable Foreign Key)
            // This links to the 'id' column on the 'allocations' table.
            // 'nullOnDelete' means if a Kantong is deleted, the transaction history remains but the link is set to null.
            $table->foreignUuid('alokasi_id')
                  ->nullable()
                  ->constrained('allocations')
                  ->nullOnDelete();
            
            // val tanggal: Long
            // In Kotlin, Dates are often stored as Long (Unix Epoch time).
            // In Laravel, it's best practice to store this as a native timestamp.
            $table->timestamp('tanggal');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};