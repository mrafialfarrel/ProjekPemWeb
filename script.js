const btnGoSignIn = document.getElementById('btnGoSignIn');
const btnGoSignUp = document.getElementById('btnGoSignUp');
const container = document.getElementById('container');
const themeToggle = document.getElementById('themeToggle');

// --- 1. LOGIKA ANIMASI GESER ---
btnGoSignIn.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});

btnGoSignUp.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
});

// --- 2. LOGIKA TEMA (DEVICE & MANUAL OVERRIDE) ---

// Fungsi untuk memperbarui teks/ikon pada tombol
function updateToggleButton() {
    if (document.body.classList.contains('dark-mode')) {
        // Mode Gelap -> Tombol beralih ke opsi Light Mode
        themeToggle.innerHTML = '<i data-feather="sun" width="16" height="16"></i> Light Mode';
    } else {
        // Mode Terang -> Tombol beralih ke opsi Dark Mode
        themeToggle.innerHTML = '<i data-feather="moon" width="16" height="16"></i> Dark Mode';
    }
    // Wajib dipanggil untuk mengubah tag <i> menjadi SVG ikon
    feather.replace();
}

// Mengecek apakah ada preferensi yang sudah disimpan sebelumnya (oleh tombol)
const savedTheme = localStorage.getItem("sakku-theme");

// Mengecek preferensi tema dari sistem/device pengguna
const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");

// Tentukan tema saat halaman pertama kali dimuat
if (savedTheme === "dark") {
    // Jika user pernah klik manual ke dark
    document.body.classList.add("dark-mode");
} else if (savedTheme === "light") {
    // Jika user pernah klik manual ke light
    document.body.classList.remove("dark-mode");
} else if (prefersDarkScheme.matches) {
    // Jika belum pernah klik manual, maka ikuti tema device (Dark)
    document.body.classList.add("dark-mode");
}

// Update tampilan tombol saat pertama kali load
updateToggleButton();

// Event Listener saat tombol ganti tema diklik
themeToggle.addEventListener('click', () => {
    // Toggle class dark-mode di body
    document.body.classList.toggle('dark-mode');
    
    // Tentukan tema apa yang sedang aktif sekarang
    let currentTheme = document.body.classList.contains('dark-mode') ? "dark" : "light";
    
    // Simpan pilihan ke LocalStorage agar diingat browser
    localStorage.setItem("sakku-theme", currentTheme);
    
    // Update tampilan tombol
    updateToggleButton();
});

// Opsional: Jika user tiba-tiba mengubah tema HP/Laptopnya saat website sedang terbuka
prefersDarkScheme.addEventListener('change', (e) => {
    // Hanya ubah secara otomatis jika user belum pernah mengatur tema secara manual
    if (!localStorage.getItem("sakku-theme")) {
        if (e.matches) {
            document.body.classList.add("dark-mode");
        } else {
            document.body.classList.remove("dark-mode");
        }
        updateToggleButton();
    }
});

// --- 3. Logika Navigasi Ke Dashboard
 const btnSubmitSignIn = document.getElementById('btnSubmitSignIn');
 const btnSubmitSignUp = document.getElementById('btnSubmitSignUp');
 
 const loginEmail = document.getElementById('loginEmail');
 const loginPassword = document.getElementById('loginPassword');

 const regName = document.getElementById('regName');
 const regEmail = document.getElementById('regEmail');
 const regPassword = document.getElementById('regPassword');

 btnSubmitSignIn.addEventListener('click', (e) => {
    e.preventDefault();

    if (loginEmail.value.trim() === '' || loginPassword.value.trim() === ''){
        alert('Ups! Email dan password akun kamu tidak boleh kosong.');
    } else {
        window.location.href = 'Dashboard.html';
    }
 });

 btnSubmitSignUp.addEventListener('click', (e) => {
    e.preventDefault();

    if (regName.value.trim() === '' || regEmail.value.trim() === '' || regPassword.value.trim() === ''){
        alert('Mohon lengkapi Nama, Email, dan Password untuk mulai mencatat keuanganmu');
    } else {
        window.location = 'Dashboard.html';
    }
 });

 // --- 4. LOGIKA TOMBOL ENTER ---

// Membuat fungsi pembantu (helper function) agar kode lebih rapi
function triggerSubmitOnEnter(inputElement, buttonElement) {
    inputElement.addEventListener('keypress', function(event) {
        // Mengecek apakah tombol yang ditekan adalah "Enter"
        if (event.key === 'Enter') {
            event.preventDefault(); // Mencegah form me-refresh halaman secara default
            buttonElement.click();  // Memerintahkan JavaScript untuk meng-klik tombol
        }
    });
}

// Menerapkan fitur Enter untuk kolom-kolom di form SIGN IN
triggerSubmitOnEnter(loginEmail, btnSubmitSignIn);
triggerSubmitOnEnter(loginPassword, btnSubmitSignIn);

// Menerapkan fitur Enter untuk kolom-kolom di form SIGN UP
triggerSubmitOnEnter(regName, btnSubmitSignUp);
triggerSubmitOnEnter(regEmail, btnSubmitSignUp);
triggerSubmitOnEnter(regPassword, btnSubmitSignUp);