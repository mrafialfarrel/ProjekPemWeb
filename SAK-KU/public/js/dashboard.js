const themeToggle = document.getElementById('themeToggle');

// Fungsi untuk mengganti dan merender ikon tema
function updateToggleButton() {
    if (document.body.classList.contains('dark-mode')) {
        // Jika mode gelap, tampilkan matahari
        themeToggle.innerHTML = '<i data-feather="sun"></i>';
    } else {
        // Jika mode terang, tampilkan bulan
        themeToggle.innerHTML = '<i data-feather="moon"></i>';
    }
    // Perintah wajib untuk merender ikon Feather yang baru dimasukkan
    feather.replace();
}

const savedTheme = localStorage.getItem("sakku-theme");
const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");

if (savedTheme === "dark") {
    document.body.classList.add("dark-mode");
} else if (savedTheme === "light") {
    document.body.classList.remove("dark-mode");
} else if (prefersDarkScheme.matches) {
    document.body.classList.add("dark-mode");
}

// Panggil fungsi ini pertama kali agar semua ikon di halaman ikut ter-render
updateToggleButton();

themeToggle.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    
    let currentTheme = document.body.classList.contains('dark-mode') ? "dark" : "light";
    localStorage.setItem("sakku-theme", currentTheme);
    
    updateToggleButton();
});