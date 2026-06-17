// --- 1. TEMA & DARK MODE SWITCHER ---
const themeToggle = document.getElementById('themeToggle');

function updateToggleButton() {
    if (!themeToggle) return;
    
    const isDark = document.body.classList.contains('dark-mode');
    const currentIcon = themeToggle.querySelector('svg')?.getAttribute('data-feather') || themeToggle.querySelector('i')?.getAttribute('data-feather');
    const expectedIcon = isDark ? 'sun' : 'moon';
    
    if (currentIcon !== expectedIcon) {
        themeToggle.innerHTML = `<i data-feather="${expectedIcon}"></i>`;
        feather.replace();
    }
    
    // Update chart theme if Chart is initialized
    if (typeof myChart !== 'undefined') {
        updateChartTheme();
    }
}

updateToggleButton();

if (themeToggle) {
    themeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        let currentTheme = document.body.classList.contains('dark-mode') ? "dark" : "light";
        localStorage.setItem("sakku-theme", currentTheme);
        document.cookie = "theme_mode=" + currentTheme + "; path=/; max-age=" + (365*24*60*60);
        updateToggleButton();
    });
}

// --- 2. LOGIKA GRAFIK KEUANGAN (Chart.js) HELPER ---
function getChartTextColor() {
    return document.body.classList.contains('dark-mode') ? '#AAAAAA' : '#777777';
}

function getChartGridColor() {
    return document.body.classList.contains('dark-mode') ? '#2D2D42' : '#DDDDDD';
}

function updateChartTheme() {
    if (typeof myChart !== 'undefined') {
        myChart.options.scales.x.ticks.color = getChartTextColor();
        myChart.options.scales.y.ticks.color = getChartTextColor();
        myChart.options.scales.y.grid.color = getChartGridColor();
        myChart.update();
    }
}

// --- 3. LOGIKA BOTTOM SHEET UNDUH ---
const downloadSheet = document.getElementById('downloadSheet');
const downloadOverlay = document.getElementById('downloadOverlay');

function openDownloadSheet() {
    if (downloadOverlay && downloadSheet) {
        downloadOverlay.classList.add('show');
        downloadSheet.classList.add('show');
    }
}

function closeDownloadSheet() {
    if (downloadOverlay && downloadSheet) {
        downloadSheet.classList.remove('show');
        downloadOverlay.classList.remove('show');
    }
}

// --- 4. LOGIKA FLOATING NOTIFICATION DROPDOWN ---
const bellBtn = document.getElementById('bellBtn');
const notificationDropdown = document.getElementById('notificationDropdown');
const notifBadge = document.getElementById('notifBadge');
const readAllNotifBtn = document.getElementById('readAllNotifBtn');
const notifDropdownBody = document.getElementById('notifDropdownBody');

if (bellBtn && notificationDropdown) {
    // Toggle dropdown
    bellBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notificationDropdown.classList.toggle('show');
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!notificationDropdown.contains(e.target) && e.target !== bellBtn && !bellBtn.contains(e.target)) {
            notificationDropdown.classList.remove('show');
        }
    });
}

// Mark single notification as read via AJAX
if (notifDropdownBody) {
    notifDropdownBody.addEventListener('click', (e) => {
        const item = e.target.closest('.notif-dropdown-item.unread');
        if (item) {
            e.stopPropagation();
            const id = item.dataset.id;
            
            // Send AJAX PATCH to mark as read
            fetch(`/notifikasi/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || getCsrfTokenFromDom(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'PATCH'
                })
            })
            .then(response => {
                if (response.ok) {
                    // Update UI: remove unread class
                    item.classList.remove('unread');
                    
                    // Decrease badge count
                    if (notifBadge) {
                        let count = parseInt(notifBadge.textContent.trim());
                        count = isNaN(count) ? 0 : count - 1;
                        if (count <= 0) {
                            notifBadge.remove();
                            if (readAllNotifBtn) readAllNotifBtn.remove();
                        } else {
                            notifBadge.textContent = count;
                        }
                    }
                }
            })
            .catch(err => console.error('Error marking notification as read:', err));
        }
    });
}

// Mark all notifications as read via AJAX
if (readAllNotifBtn) {
    readAllNotifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        
        fetch('/notifikasi/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || getCsrfTokenFromDom(),
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                // Update UI: remove all unread classes
                const unreadItems = document.querySelectorAll('.notif-dropdown-item.unread');
                unreadItems.forEach(item => item.classList.remove('unread'));
                
                // Remove badge and read-all button
                if (notifBadge) notifBadge.remove();
                readAllNotifBtn.remove();
            }
        })
        .catch(err => console.error('Error marking all notifications as read:', err));
    });
}

// Helper to get CSRF token if meta tag is not present
function getCsrfTokenFromDom() {
    const csrfInput = document.querySelector('input[name="_token"]');
    return csrfInput ? csrfInput.value : '';
}

// Logout Confirmation
const btnLogout = document.getElementById('btnLogout');
if (btnLogout) {
    btnLogout.addEventListener('click', () => {
        const konfirmasi = confirm("Apakah Anda yakin ingin keluar dari aplikasi sak-ku?");
        if (konfirmasi) {
            window.location.href = '/'; 
        }
    });
}