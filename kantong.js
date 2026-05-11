// --- 1. LOGIKA TEMA (DARK MODE) ---
const themeToggle = document.getElementById('themeToggle');

function updateToggleButton() {
    if (document.body.classList.contains('dark-mode')) {
        themeToggle.innerHTML = '<i data-feather="sun"></i>';
    } else {
        themeToggle.innerHTML = '<i data-feather="moon"></i>';
    }
    feather.replace(); // Render ikon
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

// --- 2. LOGIKA FAB MENU (Tombol Melayang) ---
const fabBtn = document.getElementById('fabBtn');
const fabMenu = document.getElementById('fabMenu');

fabBtn.addEventListener('click', () => {
    // Memunculkan menu pop up
    fabMenu.classList.toggle('show');
    // Memutar ikon plus menjadi tanda silang (X)
    fabBtn.classList.toggle('active'); 
});

// --- 3. LOGIKA BOTTOM SHEET ---
const bottomSheet = document.getElementById('bottomSheet');
const bottomSheetOverlay = document.getElementById('bottomSheetOverlay');
const sheetTitle = document.getElementById('sheetTitle');
const sheetTypeName = document.getElementById('sheetTypeName');

// Fungsi untuk membuka Bottom Sheet dengan judul yang menyesuaikan
function openBottomSheet(type) {
    // Ubah teks judul berdasarkan apa yang diklik
    sheetTitle.innerText = `Atur ${type}`;
    sheetTypeName.innerText = type;

    // Munculkan overlay dan sheet
    bottomSheetOverlay.classList.add('show');
    bottomSheet.classList.add('show');

    // Sembunyikan FAB menu jika sedang terbuka
    fabMenu.classList.remove('show');
    fabBtn.classList.remove('active');
}

// Fungsi untuk menutup Bottom Sheet
function closeBottomSheet() {
    bottomSheet.classList.remove('show');
    bottomSheetOverlay.classList.remove('show');
}