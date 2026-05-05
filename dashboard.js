// Mengambil tombol toggle dari HTML
const themeToggle = document.getElementById('themeToggle');

// Fungsi untuk update ikon pada tombol di pojok kanan atas
function updateToggleButton() {
    if (document.body.classList.contains('dark-mode')) {
        themeToggle.textContent = '☀️'; // Berubah jadi matahari saat dark mode
    } else {
        themeToggle.textContent = '🌙'; // Berubah jadi bulan saat light mode
    }
}

// 1. Cek LocalStorage (apakah user sudah milih tema di halaman login?)
const savedTheme = localStorage.getItem("sakku-theme");
const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");

// Terapkan tema sesuai riwayat atau settingan device
if (savedTheme === "dark") {
    document.body.classList.add("dark-mode");
} else if (savedTheme === "light") {
    document.body.classList.remove("dark-mode");
} else if (prefersDarkScheme.matches) {
    document.body.classList.add("dark-mode");
}

// Update ikon saat pertama kali web dimuat
updateToggleButton();

// 2. Event Listener saat tombol ganti tema diklik di Navbar
themeToggle.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    
    // Simpan pilihan ke LocalStorage agar menyinkronkan dengan halaman Login
    let currentTheme = document.body.classList.contains('dark-mode') ? "dark" : "light";
    localStorage.setItem("sakku-theme", currentTheme);
    
    updateToggleButton();
});