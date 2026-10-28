<?php
namespace Tests\Feature;
use App\Models\Apartment;
use App\Models\BuildingBlock;
use App\Models\Site;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Installer\EnvironmentWriter;
use App\Services\Installer\InstallationState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;
class InstallerTest extends TestCase
{
    use RefreshDatabase;
    private string $directory;
    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir().'/installer-test-'.bin2hex(random_bytes(4));
        mkdir($this->directory);
        file_put_contents($this->directory.'/.env', "APP_KEY=\n");
        config([
            'installer.enabled' => true,
            'installer.lock_path' => $this->directory.'/installed.lock',
            'app.key' => 'base64:'.base64_encode(random_bytes(32)),
        ]);
        $this->app->instance(EnvironmentWriter::class, new EnvironmentWriter($this->directory.'/.env', $this->directory.'/.env.example'));
        $this->app->forgetInstance(InstallationState::class);
    }
    protected function tearDown(): void
    {
        foreach (glob($this->directory.'/{,.}*', GLOB_BRACE) as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($this->directory);
        parent::tearDown();
    }
    private function sessionData(): array
    {
        return [
            'installer.done.requirements' => true,
            'installer.done.application' => true,
            'installer.done.database' => true,
            'installer.done.admin' => true,
            'installer.done.site' => true,
            'installer.done.communication' => true,
            'installer.done.visual' => true,
            'installer.data' => [
                'application' => ['app_url' => 'https://site.example.com', 'app_env' => 'production', 'app_debug' => false, 'timezone' => 'Europe/Istanbul'],
                'admin' => [
                    'name' => 'Yönetici',
                    'email' => 'yonetici@example.com',
                    'password' => Crypt::encryptString('GucluSifre2026'),
                    'security_question' => User::SECURITY_QUESTIONS[0],
                    'security_answer' => 'Pamuk',
                ],
                'site' => [
                    'name' => 'Güneş Sitesi',
                    'address' => 'Merkez Mah.',
                    'opening_balance' => '1500.50',
                    'opening_balance_date' => '2026-10-01',
                    'blocks' => [['name' => 'A Blok', 'count' => 8], ['name' => 'B Blok', 'count' => 4]],
                    'per_floor' => 4,
                ],
                'communication' => [
                    'public_url' => 'https://site.example.com',
                    'support_email' => 'destek@example.com',
                    'mail' => ['mailer' => 'log', 'host' => '', 'port' => null, 'scheme' => 'none', 'username' => '', 'password' => '', 'from_address' => 'hello@example.com', 'from_name' => 'Site360Pro'],
                    'whatsapp' => ['enabled' => false, 'api_url' => '', 'session_id' => '', 'token' => '', 'group_id' => ''],
                ],
                'visual' => [
                    'accent_color' => '#0d9488', 'glass_effect' => true, 'animations_effect' => true, 'card_hover_effect' => false,
                    'enabled' => true, 'count' => 35, 'speed' => 'slow', 'opacity' => 15, 'color_mode' => 'pastel', 'size' => 'large',
                ],
            ],
        ];
    }
    public function test_visitors_are_redirected_to_the_wizard_when_not_installed(): void
    {
        $this->get('/')->assertRedirect(route('install.index'));
        $this->get('/login')->assertRedirect(route('install.index'));
    }
    public function test_wizard_starts_with_requirements(): void
    {
        $this->get('/install')->assertRedirect(route('install.step', ['step' => 'requirements']));
        $this->get('/install/requirements')->assertOk()->assertSee('Sistem Gereksinimleri');
    }
    public function test_later_steps_are_not_accessible_before_earlier_ones(): void
    {
        $this->get('/install/admin')->assertRedirect(route('install.step', ['step' => 'requirements']));
    }
    public function test_wizard_is_closed_when_lock_file_exists(): void
    {
        file_put_contents($this->directory.'/installed.lock', '{}');
        $this->get('/install')->assertNotFound();
        $this->get('/install/requirements')->assertNotFound();
        $this->get('/')->assertRedirect('/login');
    }
    public function test_existing_installation_with_users_is_locked_automatically(): void
    {
        User::create(['name' => 'Var', 'email' => 'var@example.com', 'password' => 'Sifre12345', 'role' => 'admin']);
        $this->get('/login')->assertOk();
        $this->assertFileExists($this->directory.'/installed.lock');
        $this->get('/install')->assertNotFound();
    }
    public function test_installer_can_be_disabled(): void
    {
        config(['installer.enabled' => false]);
        $this->get('/login')->assertOk();
        $this->get('/install')->assertRedirect('/login');
    }
    public function test_admin_step_rejects_weak_passwords(): void
    {
        $this->withSession(['installer.done.requirements' => true, 'installer.done.application' => true])
            ->post('/install/admin', [
                'name' => 'Yönetici',
                'email' => 'yonetici@example.com',
                'password' => 'admin123',
                'password_confirmation' => 'admin123',
                'security_question' => User::SECURITY_QUESTIONS[0],
                'security_answer' => 'Pamuk',
            ])
            ->assertSessionHasErrors('password');
    }
    public function test_admin_step_stores_encrypted_password_and_moves_on(): void
    {
        $this->withSession(['installer.done.requirements' => true, 'installer.done.application' => true])
            ->post('/install/admin', [
                'name' => 'Yönetici',
                'email' => 'Yonetici@Example.com',
                'password' => 'GucluSifre2026',
                'password_confirmation' => 'GucluSifre2026',
                'security_question' => User::SECURITY_QUESTIONS[0],
                'security_answer' => 'Pamuk',
            ])
            ->assertRedirect(route('install.step', ['step' => 'site']));
        $stored = session('installer.data.admin');
        $this->assertSame('yonetici@example.com', $stored['email']);
        $this->assertSame('GucluSifre2026', Crypt::decryptString($stored['password']));
    }
    public function test_site_step_rejects_duplicate_block_names(): void
    {
        $this->withSession(['installer.done.requirements' => true, 'installer.done.application' => true, 'installer.done.admin' => true])
            ->post('/install/site', [
                'name' => 'Güneş Sitesi',
                'opening_balance' => 0,
                'opening_balance_date' => '2026-10-01',
                'blocks' => [['name' => 'A Blok', 'count' => 5], ['name' => 'a blok', 'count' => 5]],
            ])
            ->assertSessionHasErrors('blocks.1.name');
    }
    public function test_finish_creates_admin_site_blocks_apartments_and_settings(): void
    {
        $this->withSession($this->sessionData())
            ->post('/install/finish', ['confirm' => '1'])
            ->assertOk()
            ->assertSee('Kurulum tamamlandı');
        $admin = User::where('email', 'yonetici@example.com')->firstOrFail();
        $site = Site::where('name', 'Güneş Sitesi')->firstOrFail();
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(password_verify('GucluSifre2026', $admin->password));
        $this->assertSame(2, BuildingBlock::where('site_id', $site->id)->count());
        $this->assertSame(12, Apartment::count());
        $ninth = Apartment::whereHas('buildingBlock', fn ($query) => $query->where('name', 'A Blok'))->where('number', '9')->firstOrFail();
        $this->assertSame(3, $ninth->floor_no);
        $this->assertSame('#0d9488', SystemSetting::getValue('ui_effects', 'accent_color'));
        $this->assertSame('1', SystemSetting::getValue('bubble_effects', 'enabled'));
        $this->assertSame('35', SystemSetting::getValue('bubble_effects', 'count'));
        $this->assertSame('destek@example.com', SystemSetting::getValue('general', 'support_email'));
        $this->assertFileExists($this->directory.'/installed.lock');
        $env = file_get_contents($this->directory.'/.env');
        $this->assertStringContainsString('APP_URL=https://site.example.com', $env);
        $this->assertStringContainsString('APP_ENV=production', $env);
        $this->assertStringContainsString('APP_DEBUG=false', $env);
        $this->get('/install')->assertNotFound();
    }
    public function test_finish_requires_confirmation(): void
    {
        $this->withSession($this->sessionData())
            ->post('/install/finish', [])
            ->assertSessionHasErrors('confirm');
        $this->assertSame(0, User::count());
    }
}
