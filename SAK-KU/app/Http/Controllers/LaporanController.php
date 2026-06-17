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

        // 6. Generate Data Chart
        $chartLabels = [];
        $chartIncomeData = [];
        $chartExpenseData = [];

        if ($selectedFilter === '1 Minggu') {
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $chartLabels[] = $date->locale('id')->isoFormat('dddd');
                $dayTransactions = $filteredTransactions->filter(function ($t) use ($date) {
                    return $t->tanggal->isSameDay($date);
                });
                $chartIncomeData[] = $dayTransactions->where('is_pemasukan', true)->sum('nominal');
                $chartExpenseData[] = $dayTransactions->where('is_pemasukan', false)->sum('nominal');
            }
        } elseif ($selectedFilter === '1 Bulan' || $selectedFilter === '3 Bulan') {
            $days = $selectedFilter === '1 Bulan' ? 30 : 90;
            $step = $days === 90 ? 3 : 1;
            for ($i = $days - 1; $i >= 0; $i -= $step) {
                $date = Carbon::today()->subDays($i);
                $chartLabels[] = $date->locale('id')->isoFormat('D MMM');
                
                if ($days === 90) {
                    $windowTransactions = $filteredTransactions->filter(function ($t) use ($date) {
                        return $t->tanggal->between($date->clone()->subDays(2), $date);
                    });
                } else {
                    $windowTransactions = $filteredTransactions->filter(function ($t) use ($date) {
                        return $t->tanggal->isSameDay($date);
                    });
                }
                $chartIncomeData[] = $windowTransactions->where('is_pemasukan', true)->sum('nominal');
                $chartExpenseData[] = $windowTransactions->where('is_pemasukan', false)->sum('nominal');
            }
        } else {
            $months = $selectedFilter === '6 Bulan' ? 6 : 12;
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = Carbon::today()->subMonths($i);
                $chartLabels[] = $date->locale('id')->isoFormat('MMM Y');
                $monthTransactions = $filteredTransactions->filter(function ($t) use ($date) {
                    return $t->tanggal->isSameMonth($date);
                });
                $chartIncomeData[] = $monthTransactions->where('is_pemasukan', true)->sum('nominal');
                $chartExpenseData[] = $monthTransactions->where('is_pemasukan', false)->sum('nominal');
            }
        }

        return view('report.index', compact(
            'selectedFilter',
            'totalIncome',
            'totalExpense',
            'expenseCategories',
            'incomeCategories',
            'chartLabels',
            'chartIncomeData',
            'chartExpenseData'
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