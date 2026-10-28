<?php
namespace App\Services\Installer;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InstallationState
{
    private ?bool $installed = null;
    public function __construct(private readonly EnvironmentWriter $env) {}
    public function enabled(): bool
    {
        return (bool) config('installer.enabled');
    }
    public function lockPath(): string
    {
        return (string) config('installer.lock_path');
    }
    public function isInstalled(): bool
    {
        if ($this->installed !== null) {
            return $this->installed;
        }
        if (is_file($this->lockPath())) {
            return $this->installed = true;
        }
        if ($this->hasUsers()) {
            $this->writeLock();
            return $this->installed = true;
        }
        return $this->installed = false;
    }
    public function writeLock(): bool
    {
        $payload = json_encode([
            'installed_at' => now()->toIso8601String(),
            'installer' => 'wizard',
        ]);
        $written = @file_put_contents($this->lockPath(), $payload, LOCK_EX) !== false;
        $this->installed = true;
        return $written;
    }
    public function unlock(): void
    {
        if (is_file($this->lockPath())) {
            @unlink($this->lockPath());
        }
        $this->installed = null;
    }
    public function forget(): void
    {
        $this->installed = null;
    }
    public function hasUsers(): bool
    {
        try {
            return Schema::hasTable('users') && DB::table('users')->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }
    public function isMigrated(): bool
    {
        try {
            return Schema::hasTable('users')
                && Schema::hasTable('sites')
                && Schema::hasTable('building_blocks')
                && Schema::hasTable('apartments')
                && Schema::hasTable('system_settings');
        } catch (\Throwable $e) {
            return false;
        }
    }
    public function prepareEnvironment(): void
    {
        foreach ([
            storage_path('framework/sessions'),
            storage_path('framework/cache/data'),
            storage_path('framework/views'),
            storage_path('logs'),
            base_path('bootstrap/cache'),
        ] as $directory) {
            if (! is_dir($directory)) {
                @mkdir($directory, 0775, true);
            }
        }
        if (blank(config('app.key'))) {
            if (! $this->env->ensureExists()) {
                throw new \RuntimeException('.env dosyası oluşturulamadı. Proje klasörüne yazma izni verin veya .env.example dosyasını .env olarak kopyalayın.');
            }
            $key = 'base64:'.base64_encode(Encrypter::generateKey((string) config('app.cipher')));
            try {
                $this->env->set(['APP_KEY' => $key]);
            } catch (\RuntimeException $e) {
                throw new \RuntimeException('.env dosyasına yazılamadı. Dosya izinlerini kontrol edin veya "php artisan key:generate" komutunu çalıştırın.');
            }
            config(['app.key' => $key]);
            app()->forgetInstance('encrypter');
        }
        if (! in_array(config('session.driver'), ['file', 'array'], true)) {
            config(['session.driver' => 'file']);
        }
        if (! in_array(config('cache.default'), ['file', 'array'], true)) {
            config(['cache.default' => 'file']);
        }
    }
}
