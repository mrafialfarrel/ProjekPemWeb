<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    /**
     * Endpoint ini dipanggil saat tombol "Unduh" di bottom sheet Laporan diklik
     */
    public function download(Request $request)
    {
        $format = $request->input('format', 'pdf'); // pdf atau csv
        $rentang = $request->input('rentang', '1 Bulan');
        
        $timeLimit = $this->getTimeLimit($rentang);

        // Ambil data dari database (Diurutkan dari terbaru, sesuai logika Kotlin)
        $transactions = Transaksi::where('tanggal', '>=', $timeLimit)
            ->orderBy('tanggal', 'desc')
            ->get();

        if ($format === 'csv') {
            return $this->generateCsv($transactions);
        } else {
            return $this->generatePdf($transactions, $rentang);
        }
    }

    /**
     * Fungsi pembuat CSV (Setara dengan generateCsvString di Kotlin)
     */
    private function generateCsv($transactions)
    {
        $filename = "Laporan_Keuangan_SAKKU_" . date('Ymd') . ".csv";
        
        // Membuka stream output
        $handle = fopen('php://output', 'w');
        
        // Tulis baris Header
        fputcsv($handle, ['Tanggal', 'Kategori', 'Nominal', 'Tipe']);

        // Tulis baris data
        foreach ($transactions as $item) {
            $tipe = $item->is_pemasukan ? 'Pemasukan' : 'Pengeluaran';
            $tanggal = $item->tanggal->format('d/M/Y'); // Format tanggal Carbon
            
            fputcsv($handle, [$tanggal, $item->kategori, $item->nominal, $tipe]);
        }

        fclose($handle);

        // Kembalikan sebagai respon unduhan (Download Response)
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return Response::make('', 200, $headers);
    }

    /**
     * Fungsi pembuat PDF
     */
    private function generatePdf($transactions, $rentang)
    {
        $rentangNama = match ($rentang) {
            '1w', '1 Minggu Terakhir' => '1 Minggu Terakhir',
            '2w', '2 Minggu Terakhir' => '2 Minggu Terakhir',
            '1m', '1 Bulan Terakhir'   => '1 Bulan Terakhir',
            '3m', '3 Bulan Terakhir'   => '3 Bulan Terakhir',
            'all', 'Semua Riwayat'     => 'Semua Riwayat',
            default                    => $rentang
        };

        $totalIncome = $transactions->where('is_pemasukan', true)->sum('nominal');
        $totalExpense = $transactions->where('is_pemasukan', false)->sum('nominal');

        $pdf = Pdf::loadView('export.pdf_template', compact('transactions', 'rentangNama', 'totalIncome', 'totalExpense'));
        
        $filename = "Laporan_Keuangan_SAKKU_" . date('Ymd_His') . ".pdf";
        return $pdf->download($filename);
    }

    private function getTimeLimit($filter)
    {
        $now = Carbon::now();
        return match ($filter) {
            '1w', '1 Minggu Terakhir' => $now->subDays(7),
            '2w', '2 Minggu Terakhir' => $now->subDays(14),
            '1m', '1 Bulan Terakhir'   => $now->subDays(30),
            '3m', '3 Bulan Terakhir'   => $now->subDays(90),
            'all', 'Semua Riwayat'     => Carbon::create(2000, 1, 1),
            default                    => $now->subDays(30)
        };
    }
}