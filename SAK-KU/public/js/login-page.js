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

function showError(inputElement, message) {
    inputElement.classList.add('input-error');
    let errorElement = inputElement.nextElementSibling;
    if (!errorElement || !errorElement.classList.contains('validation-error')) {
        errorElement = document.createElement('div');
        errorElement.className = 'validation-error';
        inputElement.parentNode.insertBefore(errorElement, inputElement.nextSibling);
    }
    errorElement.textContent = message;
    errorElement.style.display = 'block';
}

function clearError(inputElement) {
    inputElement.classList.remove('input-error');
    const errorElement = inputElement.nextElementSibling;
    if (errorElement && errorElement.classList.contains('validation-error')) {
        errorElement.style.display = 'none';
    }
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePasswordStrength(password) {
    const errors = [];
    if (password.length < 8) {
        errors.push("Password minimal harus 8 karakter.");
    }
    if (!/[A-Z]/.test(password)) {
        errors.push("Harus mengandung huruf kapital (A-Z).");
    }
    if (!/[a-z]/.test(password)) {
        errors.push("Harus mengandung huruf kecil (a-z).");
    }
    if (!/[0-9]/.test(password)) {
        errors.push("Harus mengandung angka (0-9).");
    }
    return errors;
}

btnSubmitSignIn.addEventListener('click', (e) => {
    e.preventDefault();
    let isValid = true;
    
    clearError(loginEmail);
    clearError(loginPassword);

    const emailValue = loginEmail.value.trim();
    const passwordValue = loginPassword.value.trim();

    if (emailValue === '') {
        showError(loginEmail, 'Email tidak boleh kosong.');
        isValid = false;
    } else if (!validateEmail(emailValue)) {
        showError(loginEmail, 'Format email tidak valid (contoh: nama@domain.com).');
        isValid = false;
    }

    if (passwordValue === '') {
        showError(loginPassword, 'Password tidak boleh kosong.');
        isValid = false;
    }

    if (isValid) {
        window.location.href = '/dashboard';
    }
});

btnSubmitSignUp.addEventListener('click', (e) => {
    e.preventDefault();
    let isValid = true;

    clearError(regName);
    clearError(regEmail);
    clearError(regPassword);

    const nameValue = regName.value.trim();
    const emailValue = regEmail.value.trim();
    const passwordValue = regPassword.value.trim();

    if (nameValue === '') {
        showError(regName, 'Nama panggilan tidak boleh kosong.');
        isValid = false;
    }

    if (emailValue === '') {
        showError(regEmail, 'Email tidak boleh kosong.');
        isValid = false;
    } else if (!validateEmail(emailValue)) {
        showError(regEmail, 'Format email tidak valid (contoh: nama@domain.com).');
        isValid = false;
    }

    if (passwordValue === '') {
        showError(regPassword, 'Password tidak boleh kosong.');
        isValid = false;
    } else {
        const passwordErrors = validatePasswordStrength(passwordValue);
        if (passwordErrors.length > 0) {
            showError(regPassword, passwordErrors.join(' '));
            isValid = false;
        }
    }

    if (isValid) {
        window.location.href = '/dashboard';
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

[loginEmail, loginPassword, regName, regEmail, regPassword].forEach(input => {
    if (input) {
        input.addEventListener('input', () => {
            clearError(input);
        });
    }
});