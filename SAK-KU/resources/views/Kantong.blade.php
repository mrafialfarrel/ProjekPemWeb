<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantong | sak-ku</title>
    <link rel="stylesheet" href="{{ asset('css/kantong.css') }}">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <header class="top-navbar">
        <div class="nav-content">
            <button class="icon-btn" onclick="window.location.href='{{ url('/dashboard') }}'">
                <i data-feather="arrow-left"></i>
            </button>
            <h1>Kantong Saya</h1>
            <div class="nav-actions">
                <button class="icon-btn" id="themeToggle"></button>
            </div>
        </div>
    </header>

    <main class="kantong-container">
        
        <section class="total-section">
            <div class="card balance-card">
                <p>Total Kekayaan</p>
                <h2>Rp {{ number_format($total_kekayaan, 0, ',', '.') }}</h2>
            </div>
        </section>

        <section class="pocket-list">
            <div class="section-header">
                <h3>Kantong</h3>
            </div>
            
            @foreach($list_kantong as $index => $k)
            <div class="pocket-item" onclick="openDetailSheet('{{ $k->id }}', '{{ $k->nama_kantong }}', '{{ $k->saldo }}', 0, 'kantong')">
                <div class="pocket-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                <div class="pocket-info">
                    <h4>{{ $k->nama_kantong }}</h4>
                    <p>Kantong Utama</p>
                </div>
                <div class="pocket-balance">
                    <h4>Rp {{ number_format($k->saldo, 0, ',', '.') }}</h4>
                </div>
            </div>
            @endforeach

            <button class="section-action-btn" onclick="openBottomSheet('Kantong')">
                <i data-feather="plus" width="16" height="16" style="margin-right: 5px;"></i> Tambah Kantong Baru
            </button>
        </section>

        <section class="savings-list">
            <div class="section-header">
                <h3>Tabungan</h3>
            </div>

            @foreach($list_tabungan as $index => $t)
            <div class="savings-item" onclick="openDetailSheet('{{ $t->id }}', '{{ $t->nama_kantong }}', '{{ $t->saldo }}', '{{ $t->target }}', 'tabungan')">
                <div class="savings-header">
                    <div class="pocket-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="pocket-info">
                        <h4>{{ $t->nama_kantong }}</h4>
                        <p>Target: Rp {{ number_format($t->target, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="goal-area">
                    <div class="goal-stats">
                        <span>Terkumpul: <strong>Rp {{ number_format($t->saldo, 0, ',', '.') }}</strong></span>
                    </div>
                    
                    @php 
                        $persen = $t->target > 0 ? ($t->saldo / $t->target) * 100 : 0; 
                        // Dibatasi maksimal 100% agar bar tidak keluar jalur
                        $persen = $persen > 100 ? 100 : $persen;
                    @endphp
                    
                    <div class="progress-track">
                        <div class="progress-fill" style="width: {{ $persen }}%;"></div>
                    </div>
                </div>
            </div>
            @endforeach

            <button class="section-action-btn" onclick="openBottomSheet('Tabungan')">
                <i data-feather="plus" width="16" height="16" style="margin-right: 5px;"></i> Tambah Tabungan Baru
            </button>
        </section>

    </main>

    <div class="fab-container">
        <button class="fab-btn" onclick="openBottomSheet('Transaksi')" title="Tambah Transaksi">
            <i data-feather="plus"></i>
        </button>
    </div>

    <div class="bottom-sheet-overlay" id="bottomSheetOverlay" onclick="closeBottomSheet()"></div>
    <div class="bottom-sheet" id="bottomSheet">
        <div class="sheet-drag-handle"></div>
        <div class="sheet-header">
            <h3 id="sheetTitle">Pengaturan</h3>
            <button class="icon-btn close-sheet-btn" onclick="closeBottomSheet()">
                <i data-feather="x" width="20" height="20"></i>
            </button>
        </div>
        
        <div class="sheet-content">
            <div id="formTransaksi" style="display: none;">
                <form action="{{ url('/transaksi') }}" methods="POST">
                    @csrf
                    <div class="form-group">
                        <label>Jenis Transaksi</label>
                        <select name="jenis" class="form-input" required>
                            <option value="keluar">Uang Keluar (Pengeluaran)</option>
                            <option value="masuk">Uang Masuk (Pemasukan)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Pilih Kantong / Tabungan</label>
                        <select name="kantong_id" class="form-input" required>
                            <option value="" disabled selected>-- Pilih Sumber Dana --</option>
                            <optgroup label="Kantong">
                                @foreach($list_kantong as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kantong }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Tabungan">
                                @foreach($list_tabungan as $t)
                                    <option value="{{ $t->id }}">{{ $t->nama_kantong }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nominal</label>
                        <input type="number" name="nominal" class="form-input" placeholder="Rp 0" required />
                    </div>

                    <div class="form-group">
                        <label>Catatan</label>
                        <input type="text" name="catatan" class="form-input" placeholder="Misal: Makan Siang / Gaji Bulanan" />
                    </div>

                    <button type="submit" class="save-btn">Simpan Transaksi</button>
                </form>
            </div>

            <div id="formKantong" style="display: none;">
                <form action="{{ url('/kantong') }}" method="POST">
                    @csrf <div class="form-group">
                        <label>Nama Kantong Baru</label>
                        <input type="text" name="nama_kantong" class="form-input" placeholder="Misal: BCA Tabungan" required />
                    </div>
                    <div class="form-group">
                        <label>Saldo Awal</label>
                        <input type="number" name="saldo" class="form-input" placeholder="Rp 0" required />
                    </div>
                    
                    <button type="submit" class="save-btn">Tambah Kantong</button>
                </form>
            </div>

            <div id="formTabungan" style="display: none;">
                <form action="{{ url('/kantong') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tipe" value="tabungan">
                    
                    <div class="form-group">
                        <label>Nama Tabungan / Target Baru</label>
                        <input type="text" name="nama_kantong" class="form-input" placeholder="Misal: Beli Sepatu Baru" required />
                    </div>
                    <div class="form-group">
                        <label>Target Nominal Tersimpan</label>
                        <input type="number" name="target" class="form-input" placeholder="Rp 0" required />
                    </div>
                    <button type="submit" class="save-btn">Tambah Tabungan</button>
                </form>
            </div>

            <div id="formDetail" style="display: none;">
                
                <form id="formEditKantong" action="" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Nama Akun</label>
                        <input type="text" name="nama_kantong" id="editNama" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label>Ubah Saldo Saat Ini</label>
                        <input type="number" name="saldo" class="form-input" id="editSaldo" required />
                    </div>

                    <div class="form-group" id="groupTarget" style="display: none;">
                        <label>Target Nominal</label>
                        <input type="number" name="target" id="editTarget" class="form-input">
                    </div>
                    
                    <button type="submit" class="save-btn" style="margin-bottom: 10px;">Simpan Perubahan Saldo</button>
                </form>

                <form id="formDeleteKantong" action="" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin menghapus kantong ini? Semua data di dalamnya akan hilang.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="save-btn" style="background-color: #e53935; margin-bottom: 25px;">Hapus Kantong</button>
                </form>

                <div class="form-group">
                    <label>Riwayat Transaksi (Segera Hadir)</label>
                    <div class="history-list">
                        <p style="text-align: center; font-size: 12px; color: gray;">Belum ada sistem transaksi.</p>
                    </div>
                </div>
                
            </div>

        </div>
    </div>

    <script src="{{ asset('js/kantong.js') }}"></script>
</body>
</html>