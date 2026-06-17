@extends('layouts.app')

@section('title', 'Laporan | SAK-KU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/laporan.css') }}">
@endpush

@push('head-scripts')
    <!-- Specific library for this page, pushed into the <head> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <header class="top-navbar">
        <div class="nav-content">
            <button class="icon-btn" onclick="window.location.href='{{ url('/dashboard') }}'">
                <i data-feather="arrow-left"></i>
            </button>
            <h1>Laporan Keuangan</h1>
            <div class="nav-actions">
                <button class="icon-btn" id="themeToggle"></button>
            </div>
        </div>
    </header>

    <main class="laporan-container">
        @if(session('info'))
            <div class="alert alert-info" style="background-color: rgba(33, 150, 243, 0.1); color: #2196F3; padding: 12px; border-radius: 8px; margin-bottom: 15px; border: 1px solid rgba(33, 150, 243, 0.2); font-size: 13px;">
                {{ session('info') }}
            </div>
        @endif
        
        <div class="action-bar">
            <button class="download-btn" onclick="openDownloadSheet()">
                <i data-feather="download" width="16" height="16"></i> Unduh
            </button>
            
            <div class="time-filter">
                @foreach(['1 Minggu', '1 Bulan', '3 Bulan', '6 Bulan', '1 Tahun'] as $filterOpt)
                    <button class="filter-btn {{ $selectedFilter === $filterOpt ? 'active' : '' }}" 
                            onclick="window.location.href='{{ url('/laporan?filter=' . urlencode($filterOpt)) }}'">
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
                    <p>Total Pemasukan</p>
                    <h4>Rp {{ number_format($totalIncome, 0, ',', '.') }}</h4>
                </div>
            </div>
            <div class="summary-box expense-box">
                <div class="sum-icon"><i data-feather="arrow-up-circle"></i></div>
                <div class="sum-text">
                    <p>Total Pengeluaran</p>
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

        <section class="category-section">
            <h3>Kategori Pengeluaran</h3>
            @forelse ($expenseCategories as $item)
                @php
                    $catIcon = getCategoryIcon($item['name']);
                    $catColor = getCategoryColor($item['name']);
                    $percentage = $totalExpense > 0 ? round(($item['amount'] / $totalExpense) * 100) : 0;
                @endphp
                <div class="category-item">
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
                <p style="text-align: center; color: var(--text-secondary); margin: 20px 0;">Belum ada kategori pengeluaran pada periode ini.</p>
            @endforelse
        </section>

        @if($incomeCategories->isNotEmpty())
            <section class="category-section" style="margin-top: 30px;">
                <h3>Kategori Pemasukan</h3>
                @foreach ($incomeCategories as $item)
                    @php
                        $catIcon = getCategoryIcon($item['name']);
                        $catColor = getCategoryColor($item['name']);
                        $percentage = $totalIncome > 0 ? round(($item['amount'] / $totalIncome) * 100) : 0;
                    @endphp
                    <div class="category-item">
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
            </section>
        @endif

    </main>

    <div class="bottom-sheet-overlay" id="downloadOverlay" onclick="closeDownloadSheet()"></div>
    <div class="bottom-sheet" id="downloadSheet">
        <div class="sheet-drag-handle"></div>
        <div class="sheet-header">
            <h3>Unduh Laporan Keuangan</h3>
            <button class="icon-btn close-sheet-btn" onclick="closeDownloadSheet()">
                <i data-feather="x" width="20" height="20"></i>
            </button>
        </div>
        
        <form action="{{ url('/laporan/export') }}" method="POST" class="sheet-content">
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
    <script src="{{ asset('js/laporan.js') }}"></script>
    <script>
        // Override dynamic chart data from Laravel
        myChart.data.labels = {!! json_encode($chartLabels) !!};
        myChart.data.datasets[0].data = {!! json_encode($chartIncomeData) !!};
        myChart.data.datasets[1].data = {!! json_encode($chartExpenseData) !!};
        myChart.update();
    </script>
@endpush