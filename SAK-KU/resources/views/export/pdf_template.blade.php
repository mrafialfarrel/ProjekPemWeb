<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan SAK-KU</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 13px;
            line-height: 1.5;
            padding: 10px;
        }
        .header {
            border-bottom: 2px solid #9C27B0;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .brand-name {
            font-size: 24px;
            font-weight: bold;
            color: #9C27B0;
            margin: 0;
        }
        .brand-subtitle {
            font-size: 12px;
            color: #777777;
            margin: 2px 0 0 0;
        }
        .report-title {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #333333;
            margin: 0;
        }
        .report-date {
            text-align: right;
            font-size: 11px;
            color: #777777;
            margin: 2px 0 0 0;
        }
        
        /* Summary Section */
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #555555;
        }
        .summary-container {
            width: 100%;
            margin-bottom: 30px;
        }
        .summary-card {
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .income-card {
            background-color: #E8F5E9;
            border: 1px solid #C8E6C9;
        }
        .income-card h4 {
            color: #2E7D32;
            margin: 0;
            font-size: 11px;
            text-transform: uppercase;
        }
        .income-card p {
            color: #1B5E20;
            margin: 5px 0 0 0;
            font-size: 18px;
            font-weight: bold;
        }
        .expense-card {
            background-color: #FFEBEE;
            border: 1px solid #FFCDD2;
        }
        .expense-card h4 {
            color: #C62828;
            margin: 0;
            font-size: 11px;
            text-transform: uppercase;
        }
        .expense-card p {
            color: #B71C1C;
            margin: 5px 0 0 0;
            font-size: 18px;
            font-weight: bold;
        }
        .balance-card {
            background-color: #F3E5F5;
            border: 1px solid #E1BEE7;
        }
        .balance-card h4 {
            color: #6A1B9A;
            margin: 0;
            font-size: 11px;
            text-transform: uppercase;
        }
        .balance-card p {
            color: #4A148C;
            margin: 5px 0 0 0;
            font-size: 18px;
            font-weight: bold;
        }

        /* Transactions Table */
        .table-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #555555;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #F8F4F6;
            border: 1px solid #E0D0E0;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            color: #6A1B9A;
        }
        .data-table td {
            border: 1px solid #EEEEEE;
            padding: 10px;
            font-size: 12px;
        }
        .data-table tr:nth-child(even) {
            background-color: #FAF8FA;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-income {
            background-color: #C8E6C9;
            color: #2E7D32;
        }
        .badge-expense {
            background-color: #FFCDD2;
            color: #C62828;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #999999;
            border-top: 1px solid #EEEEEE;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="brand-name">SAK-KU</h1>
                    <p class="brand-subtitle">Aplikasi Catatan Keuangan Pribadi</p>
                </td>
                <td>
                    <h2 class="report-title">LAPORAN TRANSAKSI</h2>
                    <p class="report-date">Periode: {{ $rentangNama }}</p>
                    <p class="report-date" style="font-size: 9px; margin-top: 1px;">Diunduh pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY H:mm') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-title">Ringkasan Finansial</div>
    <table class="summary-container">
        <tr>
            <td style="width: 32%; padding-right: 2%;">
                <div class="summary-card income-card">
                    <h4>Total Pemasukan</h4>
                    <p>Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                </div>
            </td>
            <td style="width: 32%; padding-right: 2%;">
                <div class="summary-card expense-card">
                    <h4>Total Pengeluaran</h4>
                    <p>Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                </div>
            </td>
            <td style="width: 32%;">
                <div class="summary-card balance-card">
                    <h4>Selisih (Saldo)</h4>
                    <p>Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="table-title">Daftar Transaksi</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 20%;">Kategori</th>
                <th style="width: 30%;">Keterangan</th>
                <th style="width: 15%; text-align: center;">Tipe</th>
                <th style="width: 20%; text-align: right;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $item)
                <tr>
                    <td>{{ $item->tanggal->locale('id')->isoFormat('D MMM YYYY') }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td class="text-center">
                        <span class="badge {{ $item->is_pemasukan ? 'badge-income' : 'badge-expense' }}">
                            {{ $item->is_pemasukan ? 'Masuk' : 'Keluar' }}
                        </span>
                    </td>
                    <td class="text-right" style="font-weight: bold; color: {{ $item->is_pemasukan ? '#2E7D32' : '#C62828' }}">
                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #999999; padding: 20px;">Tidak ada data transaksi pada rentang waktu ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Laporan Keuangan Otomatis digenerate oleh SAK-KU &bull; Halaman 1 dari 1
    </div>

</body>
</html>
