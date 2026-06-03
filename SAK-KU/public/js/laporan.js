// --- 1. INISIALISASI FEATHER ICONS ---
feather.replace();

// --- 2. LOGIKA GRAFIK KEUANGAN (Chart.js) ---
const ctx = document.getElementById('financeChart').getContext('2d');

// Fungsi untuk menentukan warna teks grafik berdasarkan tema saat ini
function getChartTextColor() {
    return document.body.classList.contains('dark-mode') ? '#AAAAAA' : '#777777';
}

function getChartGridColor() {
    return document.body.classList.contains('dark-mode') ? '#2D2D42' : '#DDDDDD';
}

// Konfigurasi Data Grafik Simulasi
const chartData = {
    labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
    datasets: [
        {
            label: 'Pemasukan',
            data: [0, 5000000, 0, 0, 200000, 0, 0],
            borderColor: '#4CAF50',
            backgroundColor: 'rgba(76, 175, 80, 0.1)',
            borderWidth: 2,
            tension: 0.4, // Membuat garisnya melengkung halus
            fill: true
        },
        {
            label: 'Pengeluaran',
            data: [150000, 300000, 50000, 800000, 100000, 750000, 1000000],
            borderColor: '#F44336',
            backgroundColor: 'rgba(244, 67, 54, 0.1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        }
    ]
};

// Merender Grafik
let myChart = new Chart(ctx, {
    type: 'line',
    data: chartData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false // Sembunyikan legenda atas karena sudah ada kotak ringkasan di bawahnya
            }
        },
        scales: {
            x: {
                grid: { display: false },
                ticks: { color: getChartTextColor() }
            },
            y: {
                grid: { color: getChartGridColor() },
                ticks: { color: getChartTextColor() }
            }
        }
    }
});

// Fungsi untuk memperbarui warna grafik ketika tema berubah
function updateChartTheme() {
    myChart.options.scales.x.ticks.color = getChartTextColor();
    myChart.options.scales.y.ticks.color = getChartTextColor();
    myChart.options.scales.y.grid.color = getChartGridColor();
    myChart.update(); // Memicu Chart.js untuk merender ulang
}

// --- 3. LOGIKA TEMA (DARK MODE) ---
const themeToggle = document.getElementById('themeToggle');

function updateToggleButton() {
    if (document.body.classList.contains('dark-mode')) {
        themeToggle.innerHTML = '<i data-feather="sun"></i>';
    } else {
        themeToggle.innerHTML = '<i data-feather="moon"></i>';
    }
    feather.replace();
    updateChartTheme(); // Sinkronisasi warna grafik dengan tema
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

// --- 4. LOGIKA FILTER WAKTU ---
function changeFilter(buttonElement) {
    // Hapus class 'active' dari semua tombol filter
    let filters = document.querySelectorAll('.filter-btn');
    filters.forEach(btn => btn.classList.remove('active'));
    
    // Tambahkan class 'active' ke tombol yang diklik
    buttonElement.classList.add('active');
    
    // Di sini nantinya kamu bisa memanggil API untuk mengambil data grafik yang baru 
    // berdasarkan rentang waktu yang dipilih (misal 1 minggu / 1 bulan).
}

// --- 5. LOGIKA BOTTOM SHEET UNDUH ---
const downloadSheet = document.getElementById('downloadSheet');
const downloadOverlay = document.getElementById('downloadOverlay');

function openDownloadSheet() {
    downloadOverlay.classList.add('show');
    downloadSheet.classList.add('show');
}

function closeDownloadSheet() {
    downloadSheet.classList.remove('show');
    downloadOverlay.classList.remove('show');
}

function simulateDownload() {
    alert("Permintaan unduh diproses! Laporan akan di-generate sesuai pilihan Anda.");
    closeDownloadSheet();
}