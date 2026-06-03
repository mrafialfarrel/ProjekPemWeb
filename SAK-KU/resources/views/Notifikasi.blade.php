<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi | SAK-KU</title>
    <link rel="stylesheet" href="{{ asset('css/notifikasi.css') }}">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <header class="top-navbar">
        <div class="nav-content">
            <button class="icon-btn" onclick="window.location.href='{{ url('/dashboard') }}'">
                <i data-feather="arrow-left"></i>
            </button>
            
            <h1>Notifikasi</h1>
            
            <div class="nav-actions">
                <button class="icon-btn" id="themeToggle"></button>
                <button class="icon-btn" id="btnLogout" title="Keluar">
                    <i data-feather="log-out"></i>
                </button>
            </div>
        </div>
    </header>

    <main class="notif-container">
        
        <div class="notif-item unread">
            <div class="notif-icon danger">
                <i data-feather="alert-triangle"></i>
            </div>
            <div class="notif-content">
                <h4>Peringatan Kantong!</h4>
                <p>Pengeluaran untuk "Makan Siang" sudah mendekati batas budget bulan ini.</p>
                <span class="notif-time">10 menit yang lalu</span>
            </div>
        </div>

        <div class="notif-item unread">
            <div class="notif-icon success">
                <i data-feather="check-circle"></i>
            </div>
            <div class="notif-content">
                <h4>Pemasukan Tercatat</h4>
                <p>Gaji bulanan sebesar Rp 5.000.000 berhasil ditambahkan ke total saldo Anda.</p>
                <span class="notif-time">2 jam yang lalu</span>
            </div>
        </div>

        <div class="notif-item">
            <div class="notif-icon info">
                <i data-feather="info"></i>
            </div>
            <div class="notif-content">
                <h4>Fitur Laporan Baru</h4>
                <p>Cek ringkasan pengeluaran mingguanmu sekarang dengan grafik yang lebih detail.</p>
                <span class="notif-time">Kemarin</span>
            </div>
        </div>

    </main>

    <script src="{{ asset('js/notifikasi.js') }}"></script>
</body>
</html>