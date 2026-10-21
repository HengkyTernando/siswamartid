<?php

namespace Tests\Feature;

use App\Models\Penjual;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LaporanPdfTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $penjualUser;
    private Penjual $penjual;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admintest_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->penjualUser = User::create([
            'name' => 'Penjual Test',
            'email' => 'penjualtest_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'penjual',
            'is_active' => true,
        ]);

        $this->penjual = Penjual::create([
            'user_id' => $this->penjualUser->id,
            'nama_toko' => 'Toko Siswa Sukses',
            'deskripsi_toko' => 'Menjual aneka snack dan minuman.',
            'nomor_whatsapp' => '081234567890',
            'lokasi_kelas' => 'XII RPL 1',
            'status_toko' => 'buka',
        ]);
    }

    public function test_penjual_laporan_page_contains_unduh_rekap_pdf_button(): void
    {
        $response = $this->actingAs($this->penjualUser)->get(route('penjual.laporan'));

        $response->assertStatus(200);
        $response->assertSee('Unduh Rekap');
        $response->assertSee(route('penjual.laporan.pdf', ['week_offset' => 0]));
        $response->assertDontSee('Cetak Rekap');
        $response->assertDontSee('window.print()');
    }

    public function test_penjual_can_download_laporan_pdf(): void
    {
        $response = $this->actingAs($this->penjualUser)->get(route('penjual.laporan.pdf'));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('rekap-penjual-toko-siswa-sukses', (string) $response->headers->get('Content-Disposition'));
        $this->assertNotEmpty($response->getContent());
    }

    public function test_non_penjual_cannot_download_penjual_laporan_pdf(): void
    {
        // Guest
        $guestResponse = $this->get(route('penjual.laporan.pdf'));
        $guestResponse->assertRedirect(route('login'));

        // Admin attempting to access penjual PDF
        $adminResponse = $this->actingAs($this->admin)->get(route('penjual.laporan.pdf'));
        $adminResponse->assertStatus(403);
    }

    public function test_admin_laporan_page_contains_download_pdf_buttons_for_penjual_and_ulasan(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('Unduh PDF Penjual');
        $response->assertSee('Unduh PDF Ulasan');
        $response->assertSee(route('admin.laporan.penjual.pdf', ['period' => 'weekly']));
        $response->assertSee(route('admin.laporan.ulasan.pdf', ['period' => 'weekly']));

        // On ulasan category
        $ulasanResponse = $this->actingAs($this->admin)->get(route('admin.laporan.index', ['kategori' => 'ulasan']));
        $ulasanResponse->assertStatus(200);
        $ulasanResponse->assertSee('Unduh PDF Penjual');
        $ulasanResponse->assertSee('Unduh PDF Ulasan');
    }

    public function test_admin_can_download_penjual_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.penjual.pdf'));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('rekap-kinerja-penjual-weekly', (string) $response->headers->get('Content-Disposition'));
        $this->assertNotEmpty($response->getContent());
    }

    public function test_admin_can_download_ulasan_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.ulasan.pdf'));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('rekap-ulasan-weekly', (string) $response->headers->get('Content-Disposition'));
        $this->assertNotEmpty($response->getContent());
    }

    public function test_admin_can_download_pdf_via_general_route(): void
    {
        $responsePenjual = $this->actingAs($this->admin)->get(route('admin.laporan.pdf', ['kategori' => 'penjual']));
        $responsePenjual->assertStatus(200);
        $this->assertStringContainsString('application/pdf', (string) $responsePenjual->headers->get('Content-Type'));

        $responseUlasan = $this->actingAs($this->admin)->get(route('admin.laporan.pdf', ['kategori' => 'ulasan']));
        $responseUlasan->assertStatus(200);
        $this->assertStringContainsString('application/pdf', (string) $responseUlasan->headers->get('Content-Type'));
    }

    public function test_non_admin_cannot_download_admin_laporan_pdf(): void
    {
        // Guest
        $guestResponse = $this->get(route('admin.laporan.penjual.pdf'));
        $guestResponse->assertRedirect(route('login'));

        // Penjual user
        $penjualResponse = $this->actingAs($this->penjualUser)->get(route('admin.laporan.penjual.pdf'));
        $penjualResponse->assertStatus(403);
    }
}
