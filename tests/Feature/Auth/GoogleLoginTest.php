<?php

namespace Tests\Feature\Auth;

use App\Models\Penjual;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.google.client_id', 'test-client-id');
        Config::set('services.google.client_secret', 'test-client-secret');
        Config::set('services.google.redirect', 'http://siswamartid.test/auth/google/callback');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_login_page_contains_google_login_button_and_standard_form(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Lanjutkan dengan Google');
        $response->assertSee(route('google.redirect'));
        // Memastikan form login email & password tetap tersedia
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
    }

    public function test_google_redirect_initiates_oauth_when_configured(): void
    {
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('scopes')
            ->once()
            ->with(['openid', 'profile', 'email'])
            ->andReturnSelf();
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/v2/auth'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $response = $this->get(route('google.redirect'));

        $response->assertRedirect('https://accounts.google.com/o/oauth2/v2/auth');
    }

    public function test_google_redirect_returns_error_when_not_configured(): void
    {
        Config::set('services.google.client_id', null);

        $response = $this->get(route('google.redirect'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['google' => 'Login Google belum dikonfigurasi. Hubungi Admin SiswaMart.']);
    }

    public function test_google_callback_cancels_gracefully_when_user_cancels(): void
    {
        $response = $this->get(route('google.callback', ['error' => 'access_denied']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['google' => 'Login Google dibatalkan. Silakan coba lagi.']);
        $this->assertGuest();
    }

    public function test_google_callback_handles_socialite_exception(): void
    {
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')
            ->once()
            ->andThrow(new \Exception('Google OAuth API error'));

        Socialite::shouldReceive('driver')
            ->once()
            ->with('google')
            ->andReturn($provider);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['google' => 'Google tidak dapat memverifikasi login saat ini. Silakan coba lagi.']);
        $this->assertGuest();
    }

    public function test_google_callback_rejects_unverified_email(): void
    {
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-id-unverified');
        $googleUser->shouldReceive('getEmail')->andReturn('unverified@example.com');
        $googleUser->shouldReceive('getRaw')->andReturn(['email_verified' => false]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['google' => 'Gunakan akun Google dengan alamat email yang sudah terverifikasi.']);
        $this->assertGuest();
    }

    public function test_google_callback_rejects_unregistered_email(): void
    {
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-id-new');
        $googleUser->shouldReceive('getEmail')->andReturn('not_in_system_'.uniqid().'@example.com');
        $googleUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['google' => 'Email Google ini belum terdaftar di SiswaMart. Hubungi Admin untuk mendapatkan akun.']);
        $this->assertGuest();
    }

    public function test_google_callback_links_google_id_and_authenticates_admin(): void
    {
        $admin = User::create([
            'name' => 'Admin Penguji',
            'username' => 'admin_test_'.uniqid(),
            'email' => 'admin_test_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $googleId = 'google-id-'.uniqid();
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn($googleId);
        $googleUser->shouldReceive('getEmail')->andReturn($admin->email);
        $googleUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $admin->refresh();
        $this->assertSame($googleId, $admin->google_id);
        $this->assertNotNull($admin->email_verified_at);
    }

    public function test_google_callback_authenticates_penjual_by_google_id(): void
    {
        $googleId = 'google-id-'.uniqid();
        $penjualUser = User::create([
            'name' => 'Penjual Penguji',
            'username' => 'penjual_test_'.uniqid(),
            'email' => 'penjual_test_'.uniqid().'@example.com',
            'google_id' => $googleId,
            'password' => Hash::make('password123'),
            'role' => 'penjual',
            'is_active' => true,
        ]);

        Penjual::create([
            'user_id' => $penjualUser->id,
            'nama_toko' => 'Toko Uji Google',
            'nomor_whatsapp' => '081234567890',
            'lokasi_kelas' => '11 RPL 1',
            'nisn' => (string) random_int(1000000000, 9999999999),
            'alamat' => 'Bandung',
        ]);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn($googleId);
        $googleUser->shouldReceive('getEmail')->andReturn($penjualUser->email);
        $googleUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('penjual.dashboard'));
        $this->assertAuthenticatedAs($penjualUser);
    }

    public function test_google_callback_rejects_account_already_linked_to_different_google_id(): void
    {
        $user = User::create([
            'name' => 'User Multi Google',
            'username' => 'multi_test_'.uniqid(),
            'email' => 'multi_test_'.uniqid().'@example.com',
            'google_id' => 'existing-google-id-123',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('different-google-id-456');
        $googleUser->shouldReceive('getEmail')->andReturn($user->email);
        $googleUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['google' => 'Akun SiswaMart ini sudah tertaut dengan akun Google yang berbeda.']);
        $this->assertGuest();
    }

    public function test_google_callback_rejects_inactive_account(): void
    {
        $inactiveAdmin = User::create([
            'name' => 'Admin Nonaktif',
            'username' => 'inactive_'.uniqid(),
            'email' => 'inactive_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => false,
        ]);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-id-'.uniqid());
        $googleUser->shouldReceive('getEmail')->andReturn($inactiveAdmin->email);
        $googleUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['google' => 'Akun SiswaMart ini sedang dinonaktifkan. Hubungi Admin.']);
        $this->assertGuest();
    }

    public function test_google_callback_rejects_penjual_without_store_profile(): void
    {
        $penjualWithoutStore = User::create([
            'name' => 'Penjual Tanpa Toko',
            'username' => 'notoko_'.uniqid(),
            'email' => 'notoko_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'penjual',
            'is_active' => true,
        ]);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-id-'.uniqid());
        $googleUser->shouldReceive('getEmail')->andReturn($penjualWithoutStore->email);
        $googleUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['google' => 'Akun penjual belum memiliki profil toko. Hubungi Admin SiswaMart.']);
        $this->assertGuest();
    }

    public function test_standard_email_password_login_works(): void
    {
        $password = 'password123';
        $user = User::create([
            'name' => 'Penjual Reguler',
            'username' => 'reguler_'.uniqid(),
            'email' => 'reguler_'.uniqid().'@example.com',
            'password' => Hash::make($password),
            'role' => 'penjual',
            'is_active' => true,
        ]);

        Penjual::create([
            'user_id' => $user->id,
            'nama_toko' => 'Toko Reguler',
            'nomor_whatsapp' => '081234567899',
            'lokasi_kelas' => '12 AKL 1',
            'nisn' => (string) random_int(1000000000, 9999999999),
            'alamat' => 'Bandung',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('penjual.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_penjual_cannot_access_admin_dashboard(): void
    {
        $user = User::create([
            'name' => 'Penjual Role Check',
            'username' => 'rolecheck_'.uniqid(),
            'email' => 'rolecheck_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'penjual',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_google_callback_recovers_from_invalid_state_exception_via_stateless(): void
    {
        $user = User::create([
            'name' => 'Admin Stateless Recover',
            'username' => 'stateless_'.uniqid(),
            'email' => 'stateless_'.uniqid().'@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-id-stateless-'.uniqid());
        $googleUser->shouldReceive('getEmail')->andReturn($user->email);
        $googleUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        $provider = Mockery::mock(GoogleProvider::class);
        // Percobaan pertama memicu InvalidStateException (misal cookie state hilang pada browser mobile)
        $provider->shouldReceive('user')
            ->once()
            ->andThrow(new \Laravel\Socialite\Two\InvalidStateException());

        // Fallback stateless dipanggil dan berhasil
        $provider->shouldReceive('stateless')
            ->once()
            ->andReturnSelf();
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($googleUser);

        Socialite::shouldReceive('driver')
            ->twice()
            ->with('google')
            ->andReturn($provider);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}
