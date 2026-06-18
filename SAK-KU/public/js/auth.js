class Auth {
    static getRole() {
        return localStorage.getItem('sakku-role') || 'guest';
    }

    static isGuest() {
        return this.getRole() === 'guest';
    }

    static login(role = 'authenticated') {
        localStorage.setItem('sakku-role', role);
        document.cookie = `sakku-role=${role}; path=/`;
    }

    static logout() {
        localStorage.removeItem('sakku-role');
        document.cookie = `sakku-role=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
        window.location.href = '/dashboard';
    }

    static initGuestUI() {
        if (!this.isGuest()) return;

        const navActions = document.querySelector('.nav-actions');
        if (!navActions) return;

        // Hide notification and logout buttons
        const bellBtn = document.getElementById('bellBtn');
        const btnLogout = document.getElementById('btnLogout');
        if (bellBtn) bellBtn.style.display = 'none';
        if (btnLogout) btnLogout.style.display = 'none';

        // Add Login & Register Buttons
        const authButtons = document.createElement('div');
        authButtons.className = 'auth-buttons';
        authButtons.innerHTML = `
            <button class="auth-btn login-btn" onclick="Auth.redirectToLogin(true)">Login</button>
            <button class="auth-btn register-btn" onclick="Auth.redirectToLogin()">Daftar</button>
        `;
        navActions.appendChild(authButtons);

        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    static redirectToLogin(isRegister = false) {
        const currentPath = window.location.pathname + window.location.search;
        let url = `/login?returnUrl=${encodeURIComponent(currentPath)}`;
        if (isRegister) {
            url += '&mode=register';
        }
        window.location.href = url;
    }

    static checkAccess(callback, event = null) {
        if (this.isGuest()) {
            if (event) event.preventDefault();
            this.showRestrictedModal();
            return false;
        }
        
        if (typeof callback === 'function') {
            return callback();
        }
        return true;
    }

    static showRestrictedModal() {
        let modal = document.getElementById('restrictedModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'restrictedModal';
            modal.className = 'auth-modal-overlay';
            modal.innerHTML = `
                <div class="auth-modal">
                    <div class="auth-modal-icon">
                        <i data-feather="lock"></i>
                    </div>
                    <h2>Akses Terbatas</h2>
                    <p>Fitur ini hanya tersedia untuk pengguna yang telah masuk. Silakan login atau daftar untuk melanjutkan.</p>
                    <div class="auth-modal-actions">
                        <button class="auth-btn login-btn" onclick="Auth.redirectToLogin(true)">Login</button>
                        <button class="auth-btn register-btn" onclick="Auth.redirectToLogin()">Daftar</button>
                    </div>
                    <button class="auth-modal-close" onclick="document.getElementById('restrictedModal').classList.remove('active')">
                        <i data-feather="x"></i>
                    </button>
                </div>
            `;
            document.body.appendChild(modal);
            if (typeof feather !== 'undefined') feather.replace();
        }
        
        // Use timeout to allow CSS transition
        setTimeout(() => modal.classList.add('active'), 10);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    Auth.initGuestUI();

    // Attach to logout button if exists
    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', () => Auth.logout());
    }
});
