<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Alokasi;
use App\Models\Transaksi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Dapatkan atau buat Test User untuk pengujian
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'), // password default: password
                'email_verified_at' => now(), // langsung verifikasi agar bisa langsung login
            ]
        );

        $listKategoriPemasukan = ["Gaji", "Hadiah", "Uang Saku"];
        $listKategoriPengeluaran = ["Konsumsi", "Transportasi", "Darurat", "Hiburan"];

        // List untuk menyimpan Alokasi yang berhasil dibuat
        $createdAllocations = [];

        // 2. Buat 100 Alokasi (50 Kantong, 50 Tabungan)
        for ($i = 1; $i <= 100; $i++) {
            $isTabungan = ($i % 2 === 0);
            $nama = $isTabungan ? "Tabungan Demo $i" : "Kantong Demo $i";
            
            // Target nominal acak antara 500.000 hingga 10.000.000
            $target = rand(500, 10000) * 1000;

            $alokasi = Alokasi::create([
                'nama' => $nama,
                'target_nominal' => $target,
                'is_tabungan' => $isTabungan,
                'sort_order' => $i,
                'user_id' => $user->id,
            ]);

            $createdAllocations[] = $alokasi;
        }

        // 3. Buat 1000 Transaksi secara acak (Mundur hingga setahun terakhir)
        for ($i = 1; $i <= 1000; $i++) {
            $isPemasukan = (rand(1, 100) > 90); // 10% probabilitas pemasukan, 90% pengeluaran

            $keterangan = $isPemasukan ? "Pendapatan Demo $i" : "Pengeluaran Demo $i";
            
            // Nominal acak sesuai tipe transaksi
            $nominal = $isPemasukan 
                ? (rand(50, 2000) * 1000) // Pemasukan 50 Ribu - 2 Juta
                : (rand(15, 500) * 1000); // Pengeluaran 15 Ribu - 500 Ribu

            // Pilih kategori secara acak
            $kategori = $isPemasukan 
                ? $listKategoriPemasukan[array_rand($listKategoriPemasukan)] 
                : $listKategoriPengeluaran[array_rand($listKategoriPengeluaran)];

            // Pilih alokasi acak dari yang sudah dibuat
            $alokasiTerpilih = $createdAllocations[array_rand($createdAllocations)];

            // Acak tanggal hingga 360 hari ke belakang (mundur hingga setahun terakhir)
            // (Setara dengan hitungan `hariMundur * satuHariMs` di mana satuHariMs adalah 2 hari pada kode Kotlin Anda)
            $hariMundur = rand(0, 180) * 2;
            $tanggalAcak = Carbon::now()->subDays($hariMundur)->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            Transaksi::create([
                'keterangan' => $keterangan,
                'nominal' => $nominal,
                'is_pemasukan' => $isPemasukan,
                'kategori' => $kategori,
                'alokasi_id' => $alokasiTerpilih->id, // Menyimpan UUID alokasi asli agar relasi foreign key valid
                'user_id' => $user->id,
                'tanggal' => $tanggalAcak,
            ]);
        }
    }
}
