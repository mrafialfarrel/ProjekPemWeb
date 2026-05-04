const btnGoSignIn = document.getElementById('btnGoSignIn');
const btnGoSignUp = document.getElementById('btnGoSignUp');
const container = document.getElementById('container');
const themeToggle = document.getElementById('themeToggle');

// Animasi Geser
btnGoSignIn.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});

btnGoSignUp.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
});

// Fitur Light/Dark Mode
themeToggle.addEventListener('click', () => {
    // Menambah/menghapus class 'dark-mode' pada elemen body
    document.body.classList.toggle('dark-mode');
    
    // Mengubah teks dan ikon tombol berdasarkan mode yang aktif
    if (document.body.classList.contains('dark-mode')) {
        themeToggle.textContent = '☀️ Light Mode';
    } else {
        themeToggle.textContent = '🌙 Dark Mode';
    }
});