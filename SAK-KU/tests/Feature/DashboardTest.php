<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Transaksi;
use App\Models\Alokasi;
use Carbon\Carbon;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_page_returns_successful_response(): void
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_dashboard_3_months_filter_includes_today_transactions(): void
    {
        // Set fixed current time for test stability
        Carbon::setTestNow('2026-06-17 12:00:00');

        // Create an allocation first
        $alokasi = Alokasi::create([
            'nama' => 'Hiburan',
            'target_nominal' => 500000,
            'is_tabungan' => true,
        ]);

        // Create a transaction today
        $transaksi = Transaksi::create([
            'keterangan' => 'Hadiah',
            'nominal' => 100000,
            'is_pemasukan' => false,
            'kategori' => 'Hiburan',
            'alokasi_id' => $alokasi->id,
            'tanggal' => Carbon::now()
        ]);

        // Access dashboard with 3-month filter
        $response = $this->get('/dashboard?filter=3+Bulan');
        $response->assertStatus(200);

        // Get view data
        $chartExpenseData = $response->viewData('chartExpenseData');
        $chartLabels = $response->viewData('chartLabels');

        // The last element of chartExpenseData should cover today's transaction (nominal = 100000)
        $this->assertNotEmpty($chartExpenseData);
        $this->assertEquals(100000, end($chartExpenseData));
        $this->assertEquals('17 Jun', end($chartLabels));

        // Clean up setTestNow
        Carbon::setTestNow();
    }

    public function test_mark_notification_as_read_endpoint(): void
    {
        $notif = \App\Models\Notifikasi::create([
            'reference_id' => 'test_notif_1',
            'title' => 'Test Notification',
            'message' => 'This is a test notification message.',
            'type' => 'info',
            'is_read' => false
        ]);

        $response = $this->patch("/notifikasi/{$notif->id}/read");
        $response->assertStatus(302); // Redirect back

        $this->assertTrue($notif->fresh()->is_read);
    }

    public function test_mark_all_notifications_as_read_endpoint(): void
    {
        $notif1 = \App\Models\Notifikasi::create([
            'reference_id' => 'test_notif_2',
            'title' => 'Test Notification 2',
            'message' => 'This is a test notification message.',
            'type' => 'info',
            'is_read' => false
        ]);

        $notif2 = \App\Models\Notifikasi::create([
            'reference_id' => 'test_notif_3',
            'title' => 'Test Notification 3',
            'message' => 'This is a test notification message.',
            'type' => 'info',
            'is_read' => false
        ]);

        $response = $this->post('/notifikasi/read-all');
        $response->assertStatus(302); // Redirect back

        $this->assertTrue($notif1->fresh()->is_read);
        $this->assertTrue($notif2->fresh()->is_read);
    }
}
