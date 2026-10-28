<?php
namespace App\Services\Installer;
class RequirementsChecker
{
    public function __construct(
        private readonly string $basePath,
        private readonly array $config,
        private readonly EnvironmentWriter $env,
    ) {}
    public function report(): array
    {
        $groups = [
            ['title' => 'PHP Sürümü ve Ayarları', 'icon' => 'brand-php', 'items' => $this->phpItems()],
            ['title' => 'Zorunlu PHP Eklentileri', 'icon' => 'puzzle', 'items' => $this->extensionItems()],
            ['title' => 'Veritabanı Sürücüleri', 'icon' => 'database', 'items' => $this->driverItems()],
            ['title' => 'Klasör ve Dosya İzinleri', 'icon' => 'folder-check', 'items' => $this->permissionItems()],
            ['title' => 'Önerilen Eklentiler', 'icon' => 'thumb-up', 'items' => $this->recommendedItems()],
        ];
        $failed = 0;
        $warnings = 0;
        foreach ($groups as $group) {
            foreach ($group['items'] as $item) {
                $failed += $item['status'] === 'fail' ? 1 : 0;
                $warnings += $item['status'] === 'warn' ? 1 : 0;
            }
        }
        return [
            'groups' => $groups,
            'failed' => $failed,
            'warnings' => $warnings,
            'passes' => $failed === 0,
        ];
    }
    public function passes(): bool
    {
        return $this->report()['passes'];
    }
    public function availableDrivers(): array
    {
        $available = [];
        foreach ($this->config['drivers'] as $key => $driver) {
            $available[$key] = extension_loaded($driver['extension']);
        }
        return $available;
    }
    private function phpItems(): array
    {
        $minimum = $this->config['min_php'];
        $memory = $this->memoryLimitInMegabytes();
        return [
            $this->item(
                'PHP '.$minimum.' veya üzeri',
                version_compare(PHP_VERSION, $minimum, '>=') ? 'ok' : 'fail',
                'Kurulu sürüm: '.PHP_VERSION,
            ),
            $this->item(
                'Bellek sınırı (en az 128 MB önerilir)',
                $memory === null || $memory >= 128 ? 'ok' : 'warn',
                'memory_limit: '.ini_get('memory_limit'),
            ),
        ];
    }
    private function extensionItems(): array
    {
        $items = [];
        foreach ($this->config['required_extensions'] as $extension => $label) {
            $loaded = extension_loaded($extension);
            $items[] = $this->item($label, $loaded ? 'ok' : 'fail', $loaded ? 'Yüklü' : 'Yüklü değil');
        }
        return $items;
    }
    private function driverItems(): array
    {
        $available = $this->availableDrivers();
        $items = [];
        foreach ($this->config['drivers'] as $key => $driver) {
            $items[] = $this->item(
                $driver['label'].' ('.$driver['extension'].')',
                $available[$key] ? 'ok' : 'warn',
                $available[$key] ? 'Kullanılabilir' : 'Eklenti yüklü değil',
            );
        }
        if (! in_array(true, $available, true)) {
            $items[] = $this->item('En az bir veritabanı sürücüsü', 'fail', 'pdo_sqlite veya pdo_mysql eklentisi gerekli');
        }
        return $items;
    }
    private function permissionItems(): array
    {
        $items = [];
        $paths = [
            'storage/app' => 'fail',
            'storage/framework' => 'fail',
            'storage/framework/sessions' => 'fail',
            'storage/framework/views' => 'fail',
            'storage/framework/cache' => 'fail',
            'storage/logs' => 'fail',
            'bootstrap/cache' => 'fail',
            'database' => 'warn',
        ];
        foreach ($paths as $path => $severity) {
            $full = $this->basePath.'/'.$path;
            $writable = is_dir($full) && is_writable($full);
            $items[] = $this->item(
                $path.'/ yazılabilir olmalı',
                $writable ? 'ok' : $severity,
                $writable ? 'Yazılabilir' : (is_dir($full) ? 'Yazma izni yok' : 'Klasör bulunamadı'),
            );
        }
        $envDetail = $this->env->exists()
            ? ($this->env->isWritable() ? 'Yazılabilir' : 'Yazma izni yok')
            : ($this->env->isWritable() ? '.env.example üzerinden oluşturulacak' : 'Proje klasörüne yazılamıyor');
        $items[] = $this->item('.env dosyası yazılabilir olmalı', $this->env->isWritable() ? 'ok' : 'fail', $envDetail);
        $manifest = $this->basePath.'/public/build/manifest.json';
        $items[] = $this->item(
            'Ön yüz derlemesi (public/build)',
            is_file($manifest) ? 'ok' : 'fail',
            is_file($manifest) ? 'Derleme bulundu' : 'npm run build çalıştırılmalı',
        );
        return $items;
    }
    private function recommendedItems(): array
    {
        $items = [];
        foreach ($this->config['recommended_extensions'] as $extension => $label) {
            $loaded = extension_loaded($extension);
            $items[] = $this->item($label, $loaded ? 'ok' : 'warn', $loaded ? 'Yüklü' : 'Yüklü değil');
        }
        return $items;
    }
    private function item(string $label, string $status, string $detail): array
    {
        return ['label' => $label, 'status' => $status, 'detail' => $detail];
    }
    private function memoryLimitInMegabytes(): ?int
    {
        $raw = trim((string) ini_get('memory_limit'));
        if ($raw === '' || $raw === '-1') {
            return null;
        }
        $number = (int) $raw;
        return match (strtolower(substr($raw, -1))) {
            'g' => $number * 1024,
            'm' => $number,
            'k' => intdiv($number, 1024),
            default => intdiv($number, 1048576),
        };
    }
}
