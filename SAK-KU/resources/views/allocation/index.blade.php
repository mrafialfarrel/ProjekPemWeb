@extends('layouts.app')

@section('title', 'Alokasi & Transaksi | SAK-KU')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kantong.css') }}">
@endpush

@section('content')
    <header class="top-navbar">
        <div class="nav-content">
            <button class="icon-btn" onclick="window.location.href='{{ url('/dashboard') }}'">
                <i data-feather="arrow-left"></i>
            </button>
            <h1>Alokasi & Transaksi</h1>
            <div class="nav-actions">
                <button class="icon-btn" id="themeToggle"></button>
            </div>
        </div>
    </header>

    <div class="main-split-layout">
        <!-- LEFT COLUMN: ALOKASI -->
        <div class="column-card">
            <div class="column-header">
                <h2><i data-feather="pocket" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 5px;"></i> Daftar Alokasi</h2>
            </div>
            
            <div class="column-body">
                <!-- Kantong List -->
                <section class="pocket-list">
                    <div class="section-header">
                        <h3>Kantong (Dompet & Pengeluaran)</h3>
                    </div>
                    
                    @if(isset($list_kantong))
                        @forelse($list_kantong as $index => $k)
                        <div class="pocket-item" onclick="openDetailSheet('{{ $k->id }}', '{{ $k->nama }}', '{{ $k->saldo }}', 0, 'kantong')">
                            <div class="pocket-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="pocket-info">
                                <h4>{{ $k->nama }}</h4>
                                <p>Kantong Utama</p>
                            </div>
                            <div class="pocket-balance">
                                <h4>Rp {{ number_format($k->saldo, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        @empty
                        <p style="font-size: 13px; color: var(--text-secondary); text-align: center; margin: 15px 0;">Belum ada kantong.</p>
                        @endforelse
                    @endif

                    <button class="section-action-btn" onclick="openBottomSheet('Kantong')">
                        <i data-feather="plus" width="16" height="16" style="margin-right: 5px;"></i> Tambah Kantong Baru
                    </button>
                </section>

                <!-- Tabungan List -->
                <section class="savings-list">
                    <div class="section-header">
                        <h3>Tabungan & Rencana Target</h3>
                    </div>

                    @if(isset($list_tabungan))
                        @forelse($list_tabungan as $index => $t)
                        <div class="savings-item" onclick="openDetailSheet('{{ $t->id }}', '{{ $t->nama }}', '{{ $t->saldo }}', '{{ $t->target_nominal }}', 'tabungan')">
                            <div class="savings-header">
                                <div class="pocket-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                                <div class="pocket-info">
                                    <h4>{{ $t->nama }}</h4>
                                    <p>Target: Rp {{ number_format($t->target_nominal, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="goal-area">
                                <div class="goal-stats">
                                    <span>Terkumpul: <strong>Rp {{ number_format($t->saldo, 0, ',', '.') }}</strong></span>
                                </div>
                                
                                @php 
                                    $persen = $t->target_nominal > 0 ? ($t->saldo / $t->target_nominal) * 100 : 0; 
                                    $persen = $persen > 100 ? 100 : ($persen < 0 ? 0 : $persen);
                                @endphp
                                
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: {{ $persen }}%;"></div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p style="font-size: 13px; color: var(--text-secondary); text-align: center; margin: 15px 0;">Belum ada tabungan.</p>
                        @endforelse
                    @endif

                    <button class="section-action-btn" onclick="openBottomSheet('Tabungan')">
                        <i data-feather="plus" width="16" height="16" style="margin-right: 5px;"></i> Tambah Tabungan Baru
                    </button>
                </section>
            </div>
        </div>

        <!-- RIGHT COLUMN: TRANSAKSI -->
        <div class="column-card">
            <div class="column-header">
                <h2><i data-feather="file-text" style="width: 20px; height: 20px; vertical-align: middle; margin-right: 5px;"></i> Riwayat Transaksi Terbaru</h2>
                <button class="icon-btn" onclick="openBottomSheet('Transaksi')" style="color: var(--primary-color);" title="Tambah Transaksi">
                    <i data-feather="plus-circle" style="width: 24px; height: 24px;"></i>
                </button>
            </div>
            
            <div class="column-body">
                <div class="history-list" style="max-height: 580px; overflow-y: auto; padding-right: 5px;">
                    @forelse($transaksi as $t)
                        <div class="history-item">
                            <div class="history-info">
                                <div class="history-title">{{ $t->kategori }}</div>
                                <div class="history-date">{{ $t->keterangan }} &bull; {{ $t->alokasi ? $t->alokasi->nama : 'Tanpa Alokasi' }}</div>
                                <div style="font-size: 11px; color: var(--text-secondary); margin-top: 4px;">
                                    {{ $t->tanggal ? $t->tanggal->locale('id')->isoFormat('D MMM YYYY HH:mm') : '' }}
                                </div>
                            </div>
                            
                            <div class="history-right">
                                @if($t->is_pemasukan)
                                    <div class="history-amount income">+ Rp {{ number_format($t->nominal, 0, ',', '.') }}</div>
                                @else
                                    <div class="history-amount expense">- Rp {{ number_format($t->nominal, 0, ',', '.') }}</div>
                                @endif
                                
                                <div class="history-actions" style="margin-top: 10px;">
                                    {{-- Edit Transaction Button --}}
                                    <button class="trx-action-btn edit" onclick="event.stopPropagation(); editTransaksi('{{ $t->id }}', '{{ $t->keterangan }}', '{{ $t->nominal }}', '{{ $t->is_pemasukan ? 1 : 0 }}', '{{ $t->kategori }}', '{{ $t->alokasi_id }}')" title="Edit Transaksi">
                                        <i data-feather="edit-2"></i>
                                    </button>
                                    
                                    {{-- Delete Transaction Form --}}
                                    <form action="{{ url('/transaksi/' . $t->id) }}" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin menghapus transaksi ini?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="trx-action-btn delete" title="Hapus Transaksi">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p style="text-align: center; color: var(--text-secondary); padding: 30px 0; font-size: 14px;">Belum ada riwayat transaksi.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- FAB Button (on mobile, triggers add transaction) -->
    <div class="fab-container">
        <button class="fab-btn" onclick="openBottomSheet('Transaksi')" title="Tambah Transaksi">
            <i data-feather="plus"></i>
        </button>
    </div>

    <!-- Bottom Sheet Modals -->
    <div class="bottom-sheet-overlay" id="bottomSheetOverlay" onclick="closeBottomSheet()"></div>
    <div class="bottom-sheet" id="bottomSheet">
        <div class="sheet-drag-handle"></div>
        <div class="sheet-header">
            <h3 id="sheetTitle" style="color: var(--primary-color); font-weight: bold; margin: 0; font-size: 18px;">Pengaturan</h3>
            <button class="icon-btn close-sheet-btn" onclick="closeBottomSheet()">
                <i data-feather="x" width="20" height="20"></i>
            </button>
        </div>
        
        <div class="sheet-content">
            <!-- Form Transaksi (Tambah/Edit) -->
            <div id="formTransaksi" style="display: none;">
                <form id="transaksiSubmitForm" action="{{ url('/transaksi') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="transaksiFormMethod" value="POST">
                    
                    <div class="form-group">
                        <input type="text" name="keterangan" id="inputKeterangan" class="form-input" placeholder="Keterangan" />
                    </div>

                    <div class="form-group">
                        <input type="number" name="nominal" id="inputNominal" class="form-input" placeholder="Nominal (Rp)" required />
                    </div>

                    <div class="radio-group" style="display: flex; gap: 30px; margin-bottom: 20px; font-size: 16px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500;">
                            <input type="radio" name="is_pemasukan" value="1" id="radioPemasukan" checked onchange="toggleTransaksiFormType(1)" style="accent-color: var(--primary-color); width: 18px; height: 18px;">
                            Pemasukan
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500;">
                            <input type="radio" name="is_pemasukan" value="0" id="radioPengeluaran" onchange="toggleTransaksiFormType(0)" style="accent-color: var(--primary-color); width: 18px; height: 18px;">
                            Pengeluaran
                        </label>
                    </div>

                    <div class="form-group">
                        <select name="kategori" id="inputKategoriSelect" class="form-input" required>
                            <!-- Dipopulasikan secara dinamis via JS -->
                        </select>
                    </div>

                    <div class="form-group">
                        <select name="alokasi_id" id="inputAlokasiId" class="form-input" required>
                            <option value="" disabled selected>Pilih Tabungan</option>
                            @if(isset($semuaAlokasi))
                                @foreach($semuaAlokasi as $a)
                                    <option value="{{ $a->id }}" data-tabungan="{{ $a->is_tabungan ? '1' : '0' }}">{{ $a->nama }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <button type="submit" class="save-btn" id="btnSimpanTransaksi" style="background-color: var(--primary-color); color: white; border-radius: 20px; font-weight: bold; border: none; padding: 14px; font-size: 16px;">Simpan Transaksi</button>
                </form>
            </div>

            <!-- Form Alokasi Baru (Tambah) -->
            <div id="formAlokasi" style="display: none;">
                <form action="{{ url('/kantong') }}" method="POST">
                    @csrf
                    
                    <div class="radio-group" style="display: flex; gap: 30px; margin-bottom: 20px; font-size: 16px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500;">
                            <input type="radio" name="is_tabungan" value="1" id="radioTabungan" checked onchange="toggleAlokasiFormType('tabungan')" style="accent-color: var(--primary-color); width: 18px; height: 18px;">
                            Tabungan
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500;">
                            <input type="radio" name="is_tabungan" value="0" id="radioKantong" onchange="toggleAlokasiFormType('kantong')" style="accent-color: var(--primary-color); width: 18px; height: 18px;">
                            Kantong
                        </label>
                    </div>

                    <div class="form-group">
                        <input type="text" name="nama" id="inputAlokasiNama" class="form-input" placeholder="Nama Tabungan (Cth: Beli Mobil)" required />
                    </div>
                    
                    <div class="form-group">
                        <input type="number" name="target_nominal" id="inputAlokasiTarget" class="form-input" placeholder="Target Tabungan (Rp)" required />
                    </div>
                    
                    <button type="submit" class="save-btn" style="background-color: var(--primary-color); color: white; border-radius: 20px; font-weight: bold; border: none; padding: 14px; font-size: 16px;">Simpan</button>
                </form>
            </div>

            <!-- Form Detail / Edit / Delete Kantong/Tabungan -->
            <div id="formDetail" style="display: none;">
                <form id="formEditKantong" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Nama Akun</label>
                        <input type="text" name="nama" id="editNama" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label>Ubah Saldo Saat Ini (Melalui Transaksi Penyesuaian)</label>
                        <input type="number" name="saldo" class="form-input" id="editSaldo" required />
                    </div>

                    <div class="form-group" id="groupTarget" style="display: none;">
                        <label id="editTargetLabel">Target Nominal / Batas</label>
                        <input type="number" name="target_nominal" id="editTarget" class="form-input">
                    </div>
                    
                    <button type="submit" class="save-btn" style="margin-bottom: 10px; border-radius: 20px; font-weight: bold; padding: 14px;">Simpan Perubahan</button>
                </form>

                <form id="formDeleteKantong" action="" method="POST" onsubmit="return confirm('Apakah kamu yakin ingin menghapus alokasi ini? Histori transaksi alokasi ini akan tetap disimpan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="save-btn" style="background-color: #e53935; margin-bottom: 25px; border-radius: 20px; font-weight: bold; padding: 14px;">Hapus Alokasi</button>
                </form>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/kantong.js') }}"></script>
@endpush