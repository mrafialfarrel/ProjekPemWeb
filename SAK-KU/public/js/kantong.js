// --- 1. LOGIKA TEMA (DARK MODE) ---
const themeToggle = document.getElementById('themeToggle');

function updateToggleButton() {
    if (document.body.classList.contains('dark-mode')) {
        themeToggle.innerHTML = '<i data-feather="sun"></i>';
    } else {
        themeToggle.innerHTML = '<i data-feather="moon"></i>';
    }
    feather.replace(); 
}

const savedTheme = localStorage.getItem("sakku-theme");
if (savedTheme === "dark") { document.body.classList.add("dark-mode"); } 
else if (savedTheme === "light") { document.body.classList.remove("dark-mode"); }

updateToggleButton();

themeToggle.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    let currentTheme = document.body.classList.contains('dark-mode') ? "dark" : "light";
    localStorage.setItem("sakku-theme", currentTheme);
    updateToggleButton();
});

// --- 2. LOGIKA BOTTOM SHEET DINAMIS ---
const bottomSheet = document.getElementById('bottomSheet');
const bottomSheetOverlay = document.getElementById('bottomSheetOverlay');
const sheetTitle = document.getElementById('sheetTitle');

const formTransaksi = document.getElementById('formTransaksi');
const formKantong = document.getElementById('formKantong');
const formTabungan = document.getElementById('formTabungan');
const formDetail = document.getElementById('formDetail');
const detailBalanceInput = document.getElementById('detailBalanceInput');

function openBottomSheet(type) {
    formTransaksi.style.display = 'none';
    formKantong.style.display = 'none';
    formTabungan.style.display = 'none';
    formDetail.style.display = 'none';

    if (type === 'Transaksi') {
        sheetTitle.innerText = 'Transaksi Baru';
        formTransaksi.style.display = 'block';
    } else if (type === 'Kantong') {
        sheetTitle.innerText = 'Tambah Kantong Baru';
        formKantong.style.display = 'block';
    } else if (type === 'Tabungan') {
        sheetTitle.innerText = 'Tambah Tabungan Baru';
        formTabungan.style.display = 'block';
    }

    bottomSheetOverlay.classList.add('show');
    bottomSheet.classList.add('show');
}

// Pastikan parameter di dalam kurung disesuaikan menjadi 5 buah ini
function openDetailSheet(id, nama, saldo, target, tipe) {
    
    // 1. Masukkan data ke input HTML
    document.getElementById('editNama').value = nama;
    document.getElementById('editSaldo').value = saldo;
    
    // 2. Tembak URL form ke rute yang benar (misal: /kantong/5)
    document.getElementById('formEditKantong').action = '/kantong/' + id;
    document.getElementById('formDeleteKantong').action = '/kantong/' + id;
    
    // 3. Logika untuk memunculkan input Target khusus Tabungan
    const groupTarget = document.getElementById('groupTarget');
    if (tipe === 'tabungan') {
        groupTarget.style.display = 'block';
        document.getElementById('editTarget').value = target;
        document.getElementById('sheetTitle').innerText = 'Detail Tabungan';
    } else {
        groupTarget.style.display = 'none';
        document.getElementById('sheetTitle').innerText = 'Detail Kantong';
    }

    // 4. Logika menampilin sheet (pertahankan kode aslimu di bawah ini)
    document.getElementById('formTransaksi').style.display = 'none';
    document.getElementById('formKantong').style.display = 'none';
    document.getElementById('formTabungan').style.display = 'none';
    document.getElementById('formDetail').style.display = 'block'; // Tampilkan form detail
    
    document.getElementById('bottomSheet').classList.add('show');
    document.getElementById('bottomSheetOverlay').classList.add('show');
}

function closeBottomSheet() {
    bottomSheet.classList.remove('show');
    bottomSheetOverlay.classList.remove('show');
}

// --- 3. LOGIKA TOMBOL EDIT & HAPUS TRANSAKSI ---

function editTrx(event) {
    // Mencegah klik ter-trigger ke elemen lain (meski saat ini tidak ada, ini adalah best practice)
    event.stopPropagation();
    
    // Sebagai simulasi, memunculkan popup prompt bawaan browser
    let nominalBaru = prompt("Masukkan nominal transaksi yang benar (Hanya angka):");
    
    // Mengecek jika user mengisi form prompt dan tidak cancel
    if (nominalBaru !== null && nominalBaru.trim() !== "") {
        alert("Sip! Nanti nominal ini akan diupdate ke database: Rp " + nominalBaru);
    }
}

function deleteTrx(event) {
    event.stopPropagation();
    
    // Meminta konfirmasi sebelum menghapus
    let konfirmasi = confirm("Apakah kamu yakin ingin menghapus riwayat transaksi ini? Saldo kamu akan ikut tersesuaikan.");
    
    if (konfirmasi) {
        // Mencari elemen pembungkus terdekat (.history-item) dari tombol yang diklik
        let itemTransaksi = event.currentTarget.closest('.history-item');
        
        // Menghapus elemen tersebut dari layar secara instan
        itemTransaksi.remove();
    }
}