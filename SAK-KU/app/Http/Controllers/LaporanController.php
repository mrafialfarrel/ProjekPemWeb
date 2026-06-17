<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil state filter (Default "1 Bulan")
        $selectedFilter = $request->query('filter', '1 Bulan');
        
        // 2. Dapatkan batas waktu mundur (Time Limit) menggunakan Carbon (Setara helper getTimeLimit di Kotlin)
        $timeLimit = $this->getTimeLimit($selectedFilter);

        // 3. Filter transaksi berdasarkan waktu
        $filteredTransactions = Transaksi::where('tanggal', '>=', $timeLimit)->get();

        // 4. Pisahkan Pemasukan dan Pengeluaran
        $incomes = $filteredTransactions->where('is_pemasukan', true);
        $expenses = $filteredTransactions->where('is_pemasukan', false);

        $totalIncome = $incomes->sum('nominal');
        $totalExpense = $expenses->sum('nominal');

        // 5. Kelompokkan Kategori Pengeluaran (Setara dengan groupBy.mapIndexed di Kotlin)
        // Diurutkan dari yang terbesar
        $expenseCategories = $expenses->groupBy('kategori')
            ->map(function ($items, $kategori) {
                return [
                    'name' => $kategori,
                    'amount' => $items->sum('nominal'),
                    // Catatan: Pewarnaan chart (getChartColor) idealnya diurus di frontend (Chart.js via Blade)
                ];
            })
            ->sortByDesc('amount')
            ->values(); // Reset array index

        // Kelompokkan Kategori Pemasukan
        $incomeCategories = $incomes->groupBy('kategori')
            ->map(function ($items, $kategori) {
                return [
                    'name' => $kategori,
                    'amount' => $items->sum('nominal'),
                ];
            })
            ->sortByDesc('amount')
            ->values();

        // 6. Generate Data Chart (Opsional: Jika menggunakan Chart.js, data mentah ini bisa di-encode ke JSON di Blade)
        // Array chart data ini disederhanakan karena Chart.js biasanya memproses grouping tanggal langsung di sisi client (JS)
        
        return view('report.index', compact(
            'selectedFilter',
            'totalIncome',
            'totalExpense',
            'expenseCategories',
            'incomeCategories'
        ));
    }

    /**
     * Helper untuk mendapatkan tanggal mundur berdasarkan filter string
     */
    private function getTimeLimit($filter)
    {
        $now = Carbon::now();
        return match ($filter) {
            '1 Minggu' => $now->subDays(7),
            '1 Bulan'   => $now->subDays(30),
            '3 Bulan'   => $now->subDays(90),
            '6 Bulan'   => $now->subDays(180),
            '1 Tahun'   => $now->subDays(365),
            default     => $now->subDays(30) // Default 1 bulan
        };
    }
}