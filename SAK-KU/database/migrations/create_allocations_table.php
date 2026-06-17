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
        Schema::create('allocations', function (Blueprint $table) {
            // In Android, your ID is a String (UUID). 
            // In Laravel, we use uuid() instead of the default auto-incrementing id().
            $table->uuid('id')->primary();
            
            // val nama: String
            $table->string('nama');
            
            // val targetNominal: Double
            // 15 digits total, 2 decimal places
            $table->double('target_nominal', 15, 2)->default(0); 
            
            // val isTabungan: Boolean
            $table->boolean('is_tabungan')->default(false);
            
            // Laravel automatically adds 'created_at' and 'updated_at' timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocations');
    }

    
    public function index() 
    {
    // Ambil data beserta transaksi pengiringnya (Eager Loading)
    $alokasi = Alokasi::with('transaksi')->get();

    $list_kantong = $alokasi->where('is_tabungan', false);
    $list_tabungan = $alokasi->where('is_tabungan', true);
    
    // Total kekayaan diambil dari jumlah saldo seluruh alokasi
    $total_kekayaan = $alokasi->sum('saldo'); 

    return view('kantong', compact('list_kantong', 'list_tabungan', 'total_kekayaan'));
    }
};