@extends('layouts.app')

@section('title', 'Catatan Keuangan | SAK-KU')

@push('styles')
<style>
    /* Mengadopsi palet warna dari desain SAK-KU */
    :root {
        --primary-purple: #9b28b0; /* Menyesuaikan warna header/FAB */
        --dark-blue: #15173d;
        --light-bg: #f5ecef;
        --danger-red: #d32f2f;
        --success-green: #388e3c;
        --text-gray: #757575;
    }

    body {
        background-color: var(--light-bg);
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Header bergaya Android */
    .top-header {
        background-color: var(--primary-purple);
        color: white;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .top-header h1 {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
    }

    .back-btn {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
    }

    /* Kontainer List Transaksi */
    .transaction-container {
        padding: 20px;
        padding-bottom: 80px; /* Ruang untuk FAB */
    }

    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: var(--dark-blue);
        margin-bottom: 15px;
    }

    /* Card Transaksi */
    .transaction-card {
        background: white;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .transaction-info h4 {
        margin: 0 0 4px 0;
        font-size: 16px;
        color: var(--dark-blue);
    }

    .transaction-info p {
        margin: 0 0 8px 0;
        font-size: 12px;
        color: var(--text-gray);
    }

    .transaction-info .nominal.pengeluaran {
        color: var(--danger-red);
        font-weight: 600;
        font-size: 14px;
    }

    .transaction-info .nominal.pemasukan {
        color: var(--success-green);
        font-weight: 600;
        font-size: 14px;
    }

    .transaction-actions {
        display: flex;
        gap: 10px;
    }

    .icon-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: #9e9e9e;
    }

    .icon-btn.delete { color: var(--danger-red); }

    /* FAB (Floating Action Button) */
    .fab-btn {
        position: fixed;
        bottom: 25px;
        right: 25px;
        background-color: var(--primary-purple);
        color: white;
        border: none;
        border-radius: 50%;
        width: 56px;
        height: 56px;
        font-size: 24px;
        box-shadow: 0 4px 10px rgba(155, 40, 176, 0.4);
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 100;
    }

    /* Bottom Sheet */
    .bottom-sheet-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        display: none;
        z-index: 999;
    }

    .bottom-sheet {
        position: fixed;
        bottom: -100%;
        left: 0; right: 0;
        background: white;
        border-radius: 20px 20px 0 0;
        padding: 20px;
        transition: bottom 0.3s ease-in-out;
        z-index: 1000;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    }

    .bottom-sheet.active {
        bottom: 0;
    }

    .sheet-title {
        color: var(--primary-purple);
        font-size: 18px;
        font-weight: 700;
        margin-top: 0;
        margin-bottom: 20px;
    }

    /* Form Inputs */
    .form-group {
        margin-bottom: 15px;
    }

    .form-input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .radio-group {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
        align-items: center;
    }

    .radio-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        cursor: pointer;
    }

    .save-btn {
        width: 100%;
        background-color: var(--primary-purple);
        color: white;
        padding: 14px;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
    }
</style>
@endpush

@section('content')
    <header class="top-header">
        <button class="back-btn" onclick="window.location.href='{{ url('/kantong') }}'">
            &#8592; </button>
        <h1>Catatan Keuangan</h1>
    </header>

    <main class="transaction-container">
        <div class="section-title">Riwayat Transaksi Terbaru</div>

        @forelse($transaksi as $t)
            <div class="transaction-card">
                <div class="transaction-info">
                    <h4>{{ $t->kategori }}</h4>
                    {{-- Mengambil nama alokasi melalui relasi belongsTo --}}
                    <p>{{ $t->keterangan }} • {{ $t->alokasi ? $t->alokasi->nama : 'Tanpa Alokasi' }}</p>
                    
                    @if($t->is_pemasukan)
                        <div class="nominal pemasukan">+ Rp {{ number_format($t->nominal, 0, ',', '.') }}</div>
                    @else
                        <div class="nominal pengeluaran">- Rp {{ number_format($t->nominal, 0, ',', '.') }}</div>
                    @endif
                </div>
                <div class="transaction-actions">
                    {{-- Tombol Edit --}}
                    <button class="icon-btn" onclick="editTransaksi('{{ $t->id }}', '{{ $t->keterangan }}', '{{ $t->nominal }}', '{{ $t->is_pemasukan }}', '{{ $t->kategori }}', '{{ $t->alokasi_id }}')">
                        &#9998;
                    </button>
                    {{-- Form Delete Inline --}}
                    <form action="{{ url('/transaksi/'.$t->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?');" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="icon-btn delete">&#128465;</button>
                    </form>
                </div>
            </div>
        @empty
            <p style="text-align: center; color: var(--text-gray); margin-top: 30px;">Belum ada riwayat transaksi.</p>
        @endforelse
    </main>

    <button class="fab-btn" onclick="openBottomSheet()">+</button>

    <div class="bottom-sheet-overlay" id="sheetOverlay" onclick="closeBottomSheet()"></div>
    
    <div class="bottom-sheet" id="transactionSheet">
        <h3 class="sheet-title" id="sheetTitle">Tambah Transaksi Baru</h3>
        
        <form id="transactionForm" action="{{ url('/transaksi') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            {{-- Input ini tidak ada di UI tapi dibutuhkan oleh DB (tanggal). Kita isi otomatis dengan tanggal hari ini --}}
            <input type="hidden" name="tanggal" value="{{ now()->format('Y-m-d H:i:s') }}">

            <div class="form-group">
                <input type="text" name="keterangan" id="inputKeterangan" class="form-input" placeholder="Keterangan" required>
            </div>

            <div class="form-group">
                <input type="number" name="nominal" id="inputNominal" class="form-input" placeholder="Nominal (Rp)" required>
            </div>

            <div class="radio-group">
                <label>
                    <input type="radio" name="is_pemasukan" id="radioPemasukan" value="1" required> Pemasukan
                </label>
                <label>
                    <input type="radio" name="is_pemasukan" id="radioPengeluaran" value="0"> Pengeluaran
                </label>
            </div>

            <div class="form-group">
                <input type="text" name="kategori" id="inputKategori" class="form-input" placeholder="Kategori (Makan, Gaji, dll)" required>
            </div>

            <div class="form-group">
                <select name="alokasi_id" id="inputAlokasi" class="form-input" required>
                    <option value="" disabled selected>Pilih Tabungan / Dompet</option>
                    @foreach($list_alokasi as $alokasi)
                        <option value="{{ $alokasi->id }}">{{ $alokasi->nama }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="save-btn">Simpan Transaksi</button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const sheet = document.getElementById('transactionSheet');
    const overlay = document.getElementById('sheetOverlay');
    const form = document.getElementById('transactionForm');
    const title = document.getElementById('sheetTitle');
    const methodInput = document.getElementById('formMethod');

    function openBottomSheet() {
        // Reset form untuk mode "Tambah"
        form.action = "{{ url('/transaksi') }}";
        methodInput.value = "POST";
        title.innerText = "Tambah Transaksi Baru";
        form.reset();
        
        // Animasi buka
        overlay.style.display = 'block';
        setTimeout(() => { sheet.classList.add('active'); }, 10);
    }

    function closeBottomSheet() {
        sheet.classList.remove('active');
        setTimeout(() => { overlay.style.display = 'none'; }, 300);
    }

    function editTransaksi(id, keterangan, nominal, isPemasukan, kategori, alokasiId) {
        // Setup form untuk mode "Edit"
        form.action = `/transaksi/${id}`;
        methodInput.value = "PUT"; // Method spoofing agar terbaca sebagai proses Update
        title.innerText = "Edit Transaksi";

        // Isi form dengan data yang ada
        document.getElementById('inputKeterangan').value = keterangan;
        document.getElementById('inputNominal').value = nominal;
        document.getElementById('inputKategori').value = kategori;
        document.getElementById('inputAlokasi').value = alokasiId;

        if (isPemasukan == '1') {
            document.getElementById('radioPemasukan').checked = true;
        } else {
            document.getElementById('radioPengeluaran').checked = true;
        }

        // Buka sheet
        overlay.style.display = 'block';
        setTimeout(() => { sheet.classList.add('active'); }, 10);
    }
</script>
@endpush