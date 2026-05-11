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
const prefersDarkScheme = window.matchMedia("(prefers-color-scheme: dark)");

if (savedTheme === "dark") {
    document.body.classList.add("dark-mode");
} else if (savedTheme === "light") {
    document.body.classList.remove("dark-mode");
} else if (prefersDarkScheme.matches) {
    document.body.classList.add("dark-mode");
}

updateToggleButton();

themeToggle.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    
    let currentTheme = document.body.classList.contains('dark-mode') ? "dark" : "light";
    localStorage.setItem("sakku-theme", currentTheme);
    
    updateToggleButton();
});

const btnLogout = document.getElementById('btnLogout');

btnLogout.addEventListener('click', () => {
    const konfirmasi = confirm("Apakah Anda yakin ingin keluar dari aplikasi sak-ku?");
    
    if (konfirmasi) {
        window.location.href = 'LoginPage.html'; 
    }
});