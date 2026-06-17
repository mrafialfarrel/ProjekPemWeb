@extends('layouts.app')

@section('title', 'Dashboard | SAK-KU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <header class="top-navbar">
        <div class="nav-content">
            <h1>Dashboard Keuangan</h1>
            <div class="nav-actions">
                <button class="icon-btn" id="themeToggle"></button>
                <button class="icon-btn" onclick="window.location.href='{{ url('/notifikasi') }}'" style="position: relative;" title="Notifikasi">
                    <i data-feather="bell"></i>
                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                        <span class="badge-notif" style="position: absolute; top: -5px; right: -5px; background-color: #f44336; color: white; border-radius: 50%; width: 16px; height: 16px; font-size: 10px; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 2px solid var(--primary-color);">
                            {{ $unreadNotificationsCount }}
                        </span>
                    @endif
                </button>
            </div>
        </div>
    </header>

    <main class="dashboard-container">
        <section class="summary-section">
            <div class="card balance-card">
                <p>Total Saldo Anda</p>
                <h2>Rp {{ number_format($totalSaldo ?? 0, 0, ',', '.') }}</h2>
            </div>
            
            <div class="cashflow-cards">
                <div class="card stat-card">
                    <div class="stat-header">
                        <span class="icon-income"><i data-feather="arrow-down" width="16" height="16"></i></span> Pemasukan
                    </div>
                    <h3>Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}</h3>
                </div>
                <div class="card stat-card">
                    <div class="stat-header">
                        <span class="icon-expense"><i data-feather="arrow-up" width="16" height="16"></i></span> Pengeluaran
                    </div>
                    <h3>Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </section>

        <section class="menu-section">
            <button class="menu-btn" onclick="window.location.href='{{ url('/kantong') }}'">
                <div class="menu-icon"><i data-feather="pocket"></i></div>
                <span>Alokasi & Transaksi</span>
            </button>
            <button class="menu-btn" onclick="window.location.href='{{ url('/laporan') }}'">
                <div class="menu-icon"><i data-feather="bar-chart-2"></i></div>
                <span>Laporan</span>
            </button>
        </section>

        <section class="transaction-section">
            <h3>Transaksi Terakhir</h3>
            <div class="transaction-list">
                @forelse($recentTransactions as $t)
                    <div class="transaction-item">
                        <div>
                            <span class="trx-name" style="display: block;">{{ $t->kategori }}</span>
                            <span style="font-size: 12px; color: var(--text-secondary);">
                                {{ $t->keterangan }} &bull; {{ $t->alokasi ? $t->alokasi->nama : 'Tanpa Alokasi' }}
                            </span>
                        </div>
                        <span class="trx-amount {{ $t->is_pemasukan ? 'pemasukan' : 'pengeluaran' }}" style="color: {{ $t->is_pemasukan ? '#4CAF50' : '#F44336' }}">
                            {{ $t->is_pemasukan ? '+' : '-' }} Rp {{ number_format($t->nominal, 0, ',', '.') }}
                        </span>
                    </div>
                @empty
                    <div class="transaction-item">
                        <span class="trx-name">Belum ada transaksi</span>
                        <span class="trx-amount muted">- Rp 0</span>
                    </div>
                @endforelse
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush