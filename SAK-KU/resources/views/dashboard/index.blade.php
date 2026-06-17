@extends('layouts.app')

@section('title', 'Dashboard & Laporan | SAK-KU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <header class="top-navbar">
        <div class="nav-content">
            <h1>Dashboard & Laporan</h1>
            <div class="nav-actions">
                <button class="icon-btn" id="themeToggle" title="Ubah Tema">
                    @if(request()->cookie('theme_mode', 'light') === 'dark')
                        <i data-feather="sun"></i>
                    @else
                        <i data-feather="moon"></i>
                    @endif
                </button>
                <script>
                    (function() {
                        const savedTheme = localStorage.getItem("sakku-theme");
                        const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");
                        const isDark = savedTheme === "dark" || (!savedTheme && prefersDarkScheme.matches);
                        const toggle = document.getElementById('themeToggle');
                        if (toggle) {
                            toggle.innerHTML = isDark ? '<i data-feather="sun"></i>' : '<i data-feather="moon"></i>';
                        }
                    })();
                </script>
                <div class="notification-container">
                    <button class="icon-btn" id="bellBtn" style="position: relative;" title="Notifikasi">
                        <i data-feather="bell"></i>
                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <span class="badge-notif" id="notifBadge" style="position: absolute; top: -5px; right: -5px; background-color: #f44336; color: white; border-radius: 50%; width: 16px; height: 16px; font-size: 10px; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 2px solid var(--primary-color);">
                                {{ $unreadNotificationsCount }}
                            </span>
                        @endif
                    </button>
                    
                    <!-- Floating Notification Dropdown -->
                    <div class="notification-dropdown" id="notificationDropdown">
                        <div class="dropdown-header">
                            <h3>Notifikasi</h3>
                            @if(isset($notifikasi) && $notifikasi->where('is_read', false)->isNotEmpty())
                                <button type="button" class="read-all-btn" id="readAllNotifBtn">
                                    <i data-feather="check-square" style="width: 14px; height: 14px; vertical-align: middle;"></i> Tandai Semua Dibaca
                                </button>
                            @endif
                        </div>
                        <div class="dropdown-body" id="notifDropdownBody">
                            @forelse ($notifikasi as $notif)
                                <div class="notif-dropdown-item {{ $notif->is_read ? '' : 'unread' }}" 
                                     id="notif-item-{{ $notif->id }}"
                                     data-id="{{ $notif->id }}">
                                    
                                    @php
                                        $iconClass = 'info';
                                        $iconName = 'info';
                                        if ($notif->type === 'danger') {
                                            $iconClass = 'danger';
                                            $iconName = 'alert-triangle';
                                        } elseif ($notif->type === 'warning') {
                                            $iconClass = 'warning';
                                            $iconName = 'alert-circle';
                                        } elseif ($notif->type === 'success') {
                                            $iconClass = 'success';
                                            $iconName = 'check-circle';
                                        }
                                    @endphp

                                    <div class="notif-icon {{ $iconClass }}">
                                        <i data-feather="{{ $iconName }}"></i>
                                    </div>
                                    <div class="notif-content">
                                        <h4>{{ $notif->title }}</h4>
                                        <p>{{ $notif->message }}</p>
                                        <span class="notif-time">{{ $notif->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="notif-empty" style="padding: 30px 15px; text-align: center;">
                                    <i data-feather="bell-off" style="width: 36px; height: 36px; opacity: 0.5; margin-bottom: 8px; display: block; margin: 0 auto 8px;"></i>
                                    <p style="font-size: 13px; color: var(--text-secondary);">Tidak ada notifikasi saat ini.</p>
                                </div>
                            @endforelse
                        </div>
                </div>
                <button class="icon-btn" id="btnLogout" title="Keluar">
                    <i data-feather="log-out"></i>
                </button>
            </div>
        </div>
    </header>

    <div class="main-split-layout">
        <!-- LEFT COLUMN: DASHBOARD -->
        <div class="column-card">
            <div class="column-header">
                <h2><i data-feather="home" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 5px;"></i> Ringkasan Keuangan</h2>
            </div>
            
            <div class="column-body">
                <section class="summary-section" style="grid-template-columns: 1fr; gap: 15px; margin-bottom: 0;">
                    <div class="card balance-card">
                        <p>Total Saldo Anda</p>
                        <h2>Rp {{ number_format($totalSaldo ?? 0, 0, ',', '.') }}</h2>
                    </div>
                    
                    <div class="cashflow-cards" style="flex-direction: row; gap: 15px;">
                        <div class="card stat-card" style="padding: 15px;">
                            <div class="stat-header">
                                <span class="icon-income"><i data-feather="arrow-down" width="16" height="16"></i></span> Pemasukan
                            </div>
                            <h3>Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div class="card stat-card" style="padding: 15px;">
                            <div class="stat-header">
                                <span class="icon-expense"><i data-feather="arrow-up" width="16" height="16"></i></span> Pengeluaran
                            </div>
                            <h3>Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </section>

                <button class="save-btn" onclick="window.location.href='{{ url('/kantong') }}'" style="margin-top: 20px; margin-bottom: 20px;">
                    <i data-feather="pocket" style="width: 18px; height: 18px; margin-right: 8px;"></i> Alokasi & Tabungan
                </button>

                <section class="transaction-section">
                    <h3>Transaksi Terakhir</h3>
                    <div class="transaction-list">
                        @forelse($recentTransactions as $t)
                            <div class="transaction-item" style="padding: 14px 18px;">
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
            </div>
        </div>

        <!-- RIGHT COLUMN: LAPORAN -->
        <div class="column-card">
            <div class="column-header">
                <h2><i data-feather="bar-chart-2" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 5px;"></i> Analisis & Grafik</h2>
            </div>
            
            <div class="column-body">
                <div class="action-bar">
                    <button class="download-btn" onclick="openDownloadSheet()">
                        <i data-feather="download" width="16" height="16"></i> Unduh
                    </button>
                    
                    <div class="time-filter">
                        @foreach(['1 Minggu', '1 Bulan', '3 Bulan', '6 Bulan', '1 Tahun'] as $filterOpt)
                            <button class="filter-btn {{ $selectedFilter === $filterOpt ? 'active' : '' }}" 
                                    onclick="window.location.href='{{ url('/dashboard?filter=' . urlencode($filterOpt)) }}'">
                                {{ $filterOpt }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="chart-card">
                    <canvas id="financeChart"></canvas>
                </div>

                <div class="summary-cards">
                    <div class="summary-box income-box">
                        <div class="sum-icon"><i data-feather="arrow-down-circle"></i></div>
                        <div class="sum-text">
                            <p>Total Pemasukan (Filter)</p>
                            <h4>Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <div class="summary-box expense-box">
                        <div class="sum-icon"><i data-feather="arrow-up-circle"></i></div>
                        <div class="sum-text">
                            <p>Total Pengeluaran (Filter)</p>
                            <h4>Rp {{ number_format($totalExpense, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>

                @php
                    if (!function_exists('getCategoryIcon')) {
                        function getCategoryIcon($category) {
                            $category = strtolower($category);
                            if (str_contains($category, 'makan') || str_contains($category, 'minum') || str_contains($category, 'kopi') || str_contains($category, 'kuliner')) {
                                return 'coffee';
                            }
                            if (str_contains($category, 'belanja') || str_contains($category, 'pasar') || str_contains($category, 'mall') || str_contains($category, 'toko')) {
                                return 'shopping-bag';
                            }
                            if (str_contains($category, 'tagihan') || str_contains($category, 'listrik') || str_contains($category, 'air') || str_contains($category, 'pulsa') || str_contains($category, 'internet') || str_contains($category, 'wifi') || str_contains($category, 'utilitas')) {
                                return 'wifi';
                            }
                            if (str_contains($category, 'transport') || str_contains($category, 'bensin') || str_contains($category, 'ojek') || str_contains($category, 'mobil') || str_contains($category, 'motor') || str_contains($category, 'kendaraan')) {
                                return 'truck';
                            }
                            if (str_contains($category, 'hiburan') || str_contains($category, 'nonton') || str_contains($category, 'game') || str_contains($category, 'bioskop') || str_contains($category, 'rekreasi')) {
                                return 'film';
                            }
                            if (str_contains($category, 'gaji') || str_contains($category, 'upah') || str_contains($category, 'salary') || str_contains($category, 'pendapatan')) {
                                return 'dollar-sign';
                            }
                            if (str_contains($category, 'investasi') || str_contains($category, 'saham') || str_contains($category, 'reksa') || str_contains($category, 'bunga')) {
                                return 'trending-up';
                            }
                            if (str_contains($category, 'kesehatan') || str_contains($category, 'obat') || str_contains($category, 'dokter') || str_contains($category, 'sakit')) {
                                return 'activity';
                            }
                            if (str_contains($category, 'pendidikan') || str_contains($category, 'buku') || str_contains($category, 'sekolah') || str_contains($category, 'kuliah')) {
                                return 'book-open';
                            }
                            return 'package';
                        }
                    }

                    if (!function_exists('getCategoryColor')) {
                        function getCategoryColor($category) {
                            $category = strtolower($category);
                            if (str_contains($category, 'makan') || str_contains($category, 'minum') || str_contains($category, 'kopi') || str_contains($category, 'kuliner')) {
                                return '#FF9800'; // Orange
                            }
                            if (str_contains($category, 'belanja') || str_contains($category, 'pasar') || str_contains($category, 'mall') || str_contains($category, 'toko')) {
                                return '#F44336'; // Red
                            }
                            if (str_contains($category, 'tagihan') || str_contains($category, 'listrik') || str_contains($category, 'air') || str_contains($category, 'pulsa') || str_contains($category, 'internet') || str_contains($category, 'wifi') || str_contains($category, 'utilitas')) {
                                return '#2196F3'; // Blue
                            }
                            if (str_contains($category, 'transport') || str_contains($category, 'bensin') || str_contains($category, 'ojek') || str_contains($category, 'mobil') || str_contains($category, 'motor') || str_contains($category, 'kendaraan')) {
                                return '#9C27B0'; // Purple
                            }
                            if (str_contains($category, 'hiburan') || str_contains($category, 'nonton') || str_contains($category, 'game') || str_contains($category, 'bioskop') || str_contains($category, 'rekreasi')) {
                                return '#E91E63'; // Pink
                            }
                            if (str_contains($category, 'gaji') || str_contains($category, 'upah') || str_contains($category, 'salary') || str_contains($category, 'pendapatan')) {
                                return '#4CAF50'; // Green
                            }
                            if (str_contains($category, 'investasi') || str_contains($category, 'saham') || str_contains($category, 'reksa') || str_contains($category, 'bunga')) {
                                return '#009688'; // Teal
                            }
                            if (str_contains($category, 'kesehatan') || str_contains($category, 'obat') || str_contains($category, 'dokter') || str_contains($category, 'sakit')) {
                                return '#00BCD4'; // Cyan
                            }
                            if (str_contains($category, 'pendidikan') || str_contains($category, 'buku') || str_contains($category, 'sekolah') || str_contains($category, 'kuliah')) {
                                return '#FFEB3B'; // Yellow
                            }
                            return '#9E9E9E'; // Grey
                        }
                    }
                @endphp

                <section class="category-section" style="max-height: 300px; overflow-y: auto; padding-right: 5px;">
                    <h3>Kategori Pengeluaran</h3>
                    @forelse ($expenseCategories as $item)
                        @php
                            $catIcon = getCategoryIcon($item['name']);
                            $catColor = getCategoryColor($item['name']);
                            $percentage = $totalExpense > 0 ? round(($item['amount'] / $totalExpense) * 100) : 0;
                        @endphp
                        <div class="category-item" style="padding: 12px 16px;">
                            <div class="cat-header">
                                <div class="cat-title">
                                    <div class="cat-icon" style="color: {{ $catColor }}; background: {{ $catColor }}1A;"><i data-feather="{{ $catIcon }}"></i></div>
                                    <span>{{ $item['name'] }}</span>
                                </div>
                                <span class="cat-amount">Rp {{ number_format($item['amount'], 0, ',', '.') }}</span>
                            </div>
                            <div class="cat-progress">
                                <div class="cat-fill" style="width: {{ $percentage }}%; background-color: {{ $catColor }};"></div>
                            </div>
                        </div>
                    @empty
                        <p style="text-align: center; color: var(--text-secondary); margin: 20px 0; font-size: 13px;">Belum ada kategori pengeluaran pada periode ini.</p>
                    @endforelse

                    @if($incomeCategories->isNotEmpty())
                        <h3 style="margin-top: 25px;">Kategori Pemasukan</h3>
                        @foreach ($incomeCategories as $item)
                            @php
                                $catIcon = getCategoryIcon($item['name']);
                                $catColor = getCategoryColor($item['name']);
                                $percentage = $totalIncome > 0 ? round(($item['amount'] / $totalIncome) * 100) : 0;
                            @endphp
                            <div class="category-item" style="padding: 12px 16px;">
                                <div class="cat-header">
                                    <div class="cat-title">
                                        <div class="cat-icon" style="color: {{ $catColor }}; background: {{ $catColor }}1A;"><i data-feather="{{ $catIcon }}"></i></div>
                                        <span>{{ $item['name'] }}</span>
                                    </div>
                                    <span class="cat-amount">Rp {{ number_format($item['amount'], 0, ',', '.') }}</span>
                                </div>
                                <div class="cat-progress">
                                    <div class="cat-fill" style="width: {{ $percentage }}%; background-color: {{ $catColor }};"></div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </section>
            </div>
        </div>
    </div>

    <!-- Bottom Sheet Unduh Laporan -->
    <div class="bottom-sheet-overlay" id="downloadOverlay" onclick="closeDownloadSheet()"></div>
    <div class="bottom-sheet" id="downloadSheet">
        <div class="sheet-drag-handle"></div>
        <div class="sheet-header">
            <h3 style="color: var(--primary-color); font-weight: bold; margin: 0; font-size: 18px;">Unduh Laporan Keuangan</h3>
            <button class="icon-btn close-sheet-btn" onclick="closeDownloadSheet()" style="color: var(--text-secondary);">
                <i data-feather="x" width="20" height="20"></i>
            </button>
        </div>
        
        <form action="{{ url('/laporan/export') }}" method="POST" style="margin-top: 15px;">
            @csrf
            <div class="form-group">
                <label>Pilih Jangka Waktu Laporan</label>
                <select name="rentang" class="form-input">
                    <option value="1w">1 Minggu Terakhir</option>
                    <option value="2w">2 Minggu Terakhir</option>
                    <option value="1m" selected>1 Bulan Terakhir</option>
                    <option value="3m">3 Bulan Terakhir</option>
                    <option value="all">Semua Riwayat</option>
                </select>
            </div>
            <div class="form-group">
                <label>Pilih Format File</label>
                <select name="format" class="form-input">
                    <option value="pdf">Dokumen PDF (.pdf)</option>
                    <option value="csv">Data CSV (.csv)</option>
                </select>
            </div>
            
            <button type="submit" class="save-btn">
                <i data-feather="download" width="18" height="18" style="margin-right: 8px;"></i> Mulai Unduh
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script>
        // Inisialisasi Chart.js setelah library di-load
        const ctx = document.getElementById('financeChart').getContext('2d');
        
        let myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: {!! json_encode($chartIncomeData) !!},
                        borderColor: '#4CAF50',
                        backgroundColor: 'rgba(76, 175, 80, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Pengeluaran',
                        data: {!! json_encode($chartExpenseData) !!},
                        borderColor: '#F44336',
                        backgroundColor: 'rgba(244, 67, 54, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: getChartTextColor() }
                    },
                    y: {
                        grid: { color: getChartGridColor() },
                        ticks: { color: getChartTextColor() }
                    }
                }
            }
        });
    </script>
@endpush