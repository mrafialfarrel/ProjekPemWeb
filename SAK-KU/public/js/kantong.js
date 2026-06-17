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
const formAlokasi = document.getElementById('formAlokasi');
const formDetail = document.getElementById('formDetail');

function openBottomSheet(type) {
    formTransaksi.style.display = 'none';
    formAlokasi.style.display = 'none';
    formDetail.style.display = 'none';

    if (type === 'Transaksi') {
        const form = document.getElementById('transaksiSubmitForm');
        const methodInput = document.getElementById('transaksiFormMethod');
        form.action = '/transaksi';
        methodInput.value = 'POST';
        sheetTitle.innerText = 'Tambah Transaksi Baru';
        form.reset();
        
        // Default to Pemasukan state
        document.getElementById('radioPemasukan').checked = true;
        toggleTransaksiFormType(1);
        
        formTransaksi.style.display = 'block';
    } else if (type === 'Kantong' || type === 'Tabungan' || type === 'Alokasi') {
        sheetTitle.innerText = 'Buat Alokasi Baru';
        
        if (type === 'Kantong') {
            document.getElementById('radioKantong').checked = true;
            toggleAlokasiFormType('kantong');
        } else {
            document.getElementById('radioTabungan').checked = true;
            toggleAlokasiFormType('tabungan');
        }
        
        formAlokasi.style.display = 'block';
    }

    bottomSheetOverlay.classList.add('show');
    bottomSheet.classList.add('show');
}

function openDetailSheet(id, nama, saldo, target, tipe) {
    document.getElementById('editNama').value = nama;
    document.getElementById('editSaldo').value = saldo;
    
    document.getElementById('formEditKantong').action = '/kantong/' + id;
    document.getElementById('formDeleteKantong').action = '/kantong/' + id;
    
    const groupTarget = document.getElementById('groupTarget');
    const targetLabel = document.getElementById('editTargetLabel');
    if (tipe === 'tabungan') {
        groupTarget.style.display = 'block';
        targetLabel.innerText = 'Target Tabungan (Rp)';
        document.getElementById('editTarget').value = target;
        sheetTitle.innerText = 'Detail Tabungan';
    } else {
        groupTarget.style.display = 'block';
        targetLabel.innerText = 'Batas Kantong (Rp)';
        document.getElementById('editTarget').value = target;
        sheetTitle.innerText = 'Detail Kantong';
    }

    formTransaksi.style.display = 'none';
    formAlokasi.style.display = 'none';
    formDetail.style.display = 'block';
    
    bottomSheet.classList.add('show');
    bottomSheetOverlay.classList.add('show');
}

function closeBottomSheet() {
    bottomSheet.classList.remove('show');
    bottomSheetOverlay.classList.remove('show');
}

// --- 3. LOGIKA EDIT TRANSAKSI ---
function editTransaksi(id, keterangan, nominal, isPemasukan, kategori, alokasiId) {
    const form = document.getElementById('transaksiSubmitForm');
    const methodInput = document.getElementById('transaksiFormMethod');

    form.action = '/transaksi/' + id;
    methodInput.value = 'PUT';
    sheetTitle.innerText = 'Edit Transaksi';

    document.getElementById('inputKeterangan').value = keterangan;
    document.getElementById('inputNominal').value = nominal;
    
    if (isPemasukan == 1) {
        document.getElementById('radioPemasukan').checked = true;
        toggleTransaksiFormType(1);
    } else {
        document.getElementById('radioPengeluaran').checked = true;
        toggleTransaksiFormType(0);
    }
    
    document.getElementById('inputKategoriSelect').value = kategori;
    document.getElementById('inputAlokasiId').value = alokasiId;

    formTransaksi.style.display = 'block';
    formAlokasi.style.display = 'none';
    formDetail.style.display = 'none';

    bottomSheetOverlay.classList.add('show');
    bottomSheet.classList.add('show');
}

// --- 4. UTILITY HELPERS FOR DYNAMIC INPUTS ---
function toggleAlokasiFormType(type) {
    const namaInput = document.getElementById('inputAlokasiNama');
    const targetInput = document.getElementById('inputAlokasiTarget');
    
    if (type === 'tabungan') {
        namaInput.placeholder = 'Nama Tabungan (Cth: Beli Mobil)';
        targetInput.placeholder = 'Target Tabungan (Rp)';
    } else {
        namaInput.placeholder = 'Nama Kantong (Cth: Jajan Bulanan)';
        targetInput.placeholder = 'Batas Kantong (Rp)';
    }
}

function toggleTransaksiFormType(isPemasukan) {
    const kategoriSelect = document.getElementById('inputKategoriSelect');
    const alokasiSelect = document.getElementById('inputAlokasiId');
    
    kategoriSelect.innerHTML = '';
    
    const pemasukanCategories = ['Gaji', 'Investasi', 'Transfer Masuk', 'Penyesuaian', 'Lainnya'];
    const pengeluaranCategories = ['Makanan & Minuman', 'Transportasi', 'Tagihan & Utilitas', 'Belanja Bulanan', 'Hiburan', 'Lainnya'];
    
    const categories = isPemasukan ? pemasukanCategories : pengeluaranCategories;
    
    const defaultCatOption = document.createElement('option');
    defaultCatOption.value = '';
    defaultCatOption.disabled = true;
    defaultCatOption.selected = true;
    defaultCatOption.text = isPemasukan ? 'Kategori Pemasukan' : 'Kategori Pengeluaran';
    kategoriSelect.appendChild(defaultCatOption);
    
    categories.forEach(cat => {
        const opt = document.createElement('option');
        opt.value = cat;
        opt.text = cat;
        kategoriSelect.appendChild(opt);
    });

    const firstOption = alokasiSelect.options[0];
    firstOption.text = isPemasukan ? 'Pilih Tabungan' : 'Pilih Kantong';
    
    for (let i = 1; i < alokasiSelect.options.length; i++) {
        const opt = alokasiSelect.options[i];
        const isTabungan = opt.getAttribute('data-tabungan') === '1';
        if (isPemasukan) {
            opt.style.display = isTabungan ? 'block' : 'none';
        } else {
            opt.style.display = isTabungan ? 'none' : 'block';
        }
    }
    alokasiSelect.selectedIndex = 0;
}