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
                <button class="icon-btn" onclick="window.location.href='{{ url('/notifikasi') }}'">
                    <i data-feather="bell"></i>
                </button>
            </div>
        </div>
    </header>

    <main class="dashboard-container">
        <section class="summary-section">
            <div class="card balance-card">
                <p>Total Saldo Anda</p>
                <h2>Rp 0</h2>
            </div>
            
            <div class="cashflow-cards">
                <div class="card stat-card">
                    <div class="stat-header">
                        <span class="icon-income"><i data-feather="arrow-down" width="16" height="16"></i></span> Pemasukan
                    </div>
                    <h3>Rp 0</h3>
                </div>
                <div class="card stat-card">
                    <div class="stat-header">
                        <span class="icon-expense"><i data-feather="arrow-up" width="16" height="16"></i></span> Pengeluaran
                    </div>
                    <h3>Rp 0</h3>
                </div>
            </div>
        </section>

        <section class="menu-section">
            <button class="menu-btn" onclick="window.location.href='{{ url('/kantong') }}'">
                <div class="menu-icon"><i data-feather="pocket"></i></div>
                <span>Kantong</span>
            </button>
            <button class="menu-btn" onclick="window.location.href='{{ url('/laporan') }}'">
                <div class="menu-icon"><i data-feather="bar-chart-2"></i></div>
                <span>Laporan</span>
            </button>
        </section>

        <section class="transaction-section">
            <h3>Transaksi Terakhir</h3>
            <div class="transaction-list">
                <div class="transaction-item">
                    <span class="trx-name">Belum ada transaksi</span>
                    <span class="trx-amount muted">- Rp 0</span>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush