<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

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
        /*
         * PENTING: Di Laravel, kita jarang menggambar canvas PDF baris-demi-baris seperti di Android (drawText).
         * Praktik terbaik Laravel adalah membuat file HTML biasa (misal: resources/views/export/pdf.blade.php),
         * lalu mengubah HTML tersebut menjadi PDF menggunakan package tambahan.
         */
         
        // Contoh jika Anda sudah menginstall library dompdf:
        // $pdf = Pdf::loadView('export.pdf_template', compact('transactions', 'rentang'));
        // return $pdf->download("Laporan_Keuangan_$rentang.pdf");
        
        return back()->with('info', 'Fitur PDF memerlukan package seperti barryvdh/laravel-dompdf untuk dirender dari HTML.');
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