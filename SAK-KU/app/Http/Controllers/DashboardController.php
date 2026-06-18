<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // === LOGIKA DASHBOARD ===
        // 1. Ambil seluruh transaksi untuk menghitung total
        $semuaTransaksi = Transaksi::where('user_id', Auth::id())->get();

        $totalPemasukan = $semuaTransaksi->where('is_pemasukan', true)->sum('nominal');
        $totalPengeluaran = $semuaTransaksi->where('is_pemasukan', false)->sum('nominal');
        $totalSaldo = $totalPemasukan - $totalPengeluaran;

        // 2. Ambil 5 transaksi terbaru
        $recentTransactions = Transaksi::with('alokasi')
            ->where('user_id', Auth::id())
            ->orderBy('tanggal', 'desc')
            ->take(5)
            ->get();

        // 3. Pengaturan UI (Tema & Notifikasi)
        $selectedTheme = request()->cookie('theme_mode', 'system');
        $isNotificationEnabled = request()->cookie('notification_enabled', true);

        // 4. Proses dan Ambil data Notifikasi
        if (Auth::check()) {
            Notifikasi::generateProgressNotifications();
        }
        $notifikasi = Notifikasi::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        $unreadNotificationsCount = $notifikasi->where('is_read', false)->count();


        // === LOGIKA LAPORAN ===
        // 1. Ambil state filter (Default "1 Bulan")
        $selectedFilter = $request->query('filter', '1 Bulan');
        
        // 2. Dapatkan batas waktu mundur (Time Limit) menggunakan Carbon
        $timeLimit = $this->getTimeLimit($selectedFilter);

        // 3. Filter transaksi berdasarkan waktu
        $filteredTransactions = Transaksi::where('user_id', Auth::id())
            ->where('tanggal', '>=', $timeLimit)->get();

        // 4. Pisahkan Pemasukan dan Pengeluaran periode laporan
        $incomes = $filteredTransactions->where('is_pemasukan', true);
        $expenses = $filteredTransactions->where('is_pemasukan', false);

        $totalIncome = $incomes->sum('nominal');
        $totalExpense = $expenses->sum('nominal');

        // 5. Kelompokkan Kategori Pengeluaran
        $expenseCategories = $expenses->groupBy('kategori')
            ->map(function ($items, $kategori) {
                return [
                    'name' => $kategori,
                    'amount' => $items->sum('nominal'),
                ];
            })
            ->sortByDesc('amount')
            ->values();

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
            for ($i = $days - $step; $i >= 0; $i -= $step) {
                $date = Carbon::today()->subDays($i);
                $chartLabels[] = $date->locale('id')->isoFormat('D MMM');
                
                if ($days === 90) {
                    $windowTransactions = $filteredTransactions->filter(function ($t) use ($date) {
                        return $t->tanggal->between(
                            $date->clone()->subDays(2)->startOfDay(),
                            $date->clone()->endOfDay()
                        );
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

        return view('dashboard.index', compact(
            'totalSaldo', 
            'totalPemasukan', 
            'totalPengeluaran', 
            'recentTransactions',
            'selectedTheme',
            'isNotificationEnabled',
            'unreadNotificationsCount',
            'selectedFilter',
            'totalIncome',
            'totalExpense',
            'expenseCategories',
            'incomeCategories',
            'chartLabels',
            'chartIncomeData',
            'chartExpenseData',
            'notifikasi'
        ));
    }

    /**
     * Menyimpan preferensi tema ke Cookie browser
     */
    public function setTheme(Request $request)
    {
        $theme = $request->input('theme'); // 'light', 'dark', 'system'
        return back()->withCookie(cookie()->forever('theme_mode', $theme));
    }

    /**
     * Helper untuk mendapatkan tanggal mundur berdasarkan filter string
     */
    private function getTimeLimit($filter)
    {
        $now = Carbon::now();
        return match ($filter) {
            '1 Minggu' => $now->subDays(7)->startOfDay(),
            '1 Bulan'   => $now->subDays(30)->startOfDay(),
            '3 Bulan'   => $now->subDays(90)->startOfDay(),
            '6 Bulan'   => $now->subDays(180)->startOfDay(),
            '1 Tahun'   => $now->subDays(365)->startOfDay(),
            default     => $now->subDays(30)->startOfDay()
        };
    }
}