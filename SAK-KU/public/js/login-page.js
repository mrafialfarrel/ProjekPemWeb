const btnGoSignIn = document.getElementById('btnGoSignIn');
const btnGoSignUp = document.getElementById('btnGoSignUp');
const container = document.getElementById('container');
const themeToggle = document.getElementById('themeToggle');

btnGoSignIn.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});

btnGoSignUp.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
});

function updateToggleButton() {
    if (document.body.classList.contains('dark-mode')) {
        themeToggle.innerHTML = '<i data-feather="sun" width="16" height="16"></i> Light Mode';
    } else {
        themeToggle.innerHTML = '<i data-feather="moon" width="16" height="16"></i> Dark Mode';
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

prefersDarkScheme.addEventListener('change', (e) => {
    if (!localStorage.getItem("sakku-theme")) {
        if (e.matches) {
            document.body.classList.add("dark-mode");
        } else {
            document.body.classList.remove("dark-mode");
        }
        updateToggleButton();
    }
});

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
        window.location.href = '/dashboard';
    }
 });

 btnSubmitSignUp.addEventListener('click', (e) => {
    e.preventDefault();

    if (regName.value.trim() === '' || regEmail.value.trim() === '' || regPassword.value.trim() === ''){
        alert('Mohon lengkapi Nama, Email, dan Password untuk mulai mencatat keuanganmu');
    } else {
        window.location.href ='/dashboard';
    }
 });

function triggerSubmitOnEnter(inputElement, buttonElement) {
    inputElement.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); 
            buttonElement.click();
        }
    });
}

triggerSubmitOnEnter(loginEmail, btnSubmitSignIn);
triggerSubmitOnEnter(loginPassword, btnSubmitSignIn);

triggerSubmitOnEnter(regName, btnSubmitSignUp);
triggerSubmitOnEnter(regEmail, btnSubmitSignUp);
triggerSubmitOnEnter(regPassword, btnSubmitSignUp);