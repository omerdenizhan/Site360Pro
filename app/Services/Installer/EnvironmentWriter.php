<?php
namespace App\Services\Installer;
class EnvironmentWriter
{
    public function __construct(
        private readonly string $path,
        private readonly string $examplePath,
        private readonly ?string $configCachePath = null,
    ) {}
    public function exists(): bool
    {
        return is_file($this->path);
    }
    public function isWritable(): bool
    {
        return $this->exists()
            ? is_writable($this->path)
            : is_writable(dirname($this->path));
    }
    public function ensureExists(): bool
    {
        if ($this->exists()) {
            return true;
        }
        $contents = is_file($this->examplePath) ? (string) file_get_contents($this->examplePath) : '';
        return @file_put_contents($this->path, $contents, LOCK_EX) !== false;
    }
    public function contents(): string
    {
        return $this->exists() ? (string) file_get_contents($this->path) : '';
    }
    public function get(string $key): ?string
    {
        foreach (preg_split('/\R/', $this->contents()) as $line) {
            if (preg_match('/^\s*'.preg_quote($key, '/').'\s*=(.*)$/', $line, $matches)) {
                return $this->parse($matches[1]);
            }
        }
        return null;
    }
    public function set(array $values): void
    {
        $lines = preg_split('/\R/', $this->contents());
        if ($lines === [''] || $lines === false) {
            $lines = [];
        }
        foreach ($values as $key => $value) {
            $formatted = $key.'='.self::format($value);
            $active = $this->findLine($lines, $key, false);
            $commented = $active === null ? $this->findLine($lines, $key, true) : null;
            $index = $active ?? $commented;
            if ($index === null) {
                $lines[] = $formatted;
                continue;
            }
            $lines[$index] = $formatted;
        }
        $this->write($lines);
    }
    public function forget(array $keys): void
    {
        $lines = preg_split('/\R/', $this->contents());
        foreach ($keys as $key) {
            $index = $this->findLine($lines, $key, false);
            if ($index !== null) {
                $lines[$index] = '#'.ltrim($lines[$index]);
            }
        }
        $this->write($lines);
    }
    public function clearConfigCache(): void
    {
        if ($this->configCachePath !== null && is_file($this->configCachePath)) {
            @unlink($this->configCachePath);
        }
    }
    public static function format(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if ($value === null) {
            return 'null';
        }
        $value = (string) $value;
        if ($value === '') {
            return '';
        }
        if (preg_match('/^[A-Za-z0-9_.:\/@,+=-]+$/', $value)) {
            return $value;
        }
        return '"'.str_replace(['\\', '"', '$', "\r", "\n"], ['\\\\', '\\"', '\\$', '\\r', '\\n'], $value).'"';
    }
    private function parse(string $raw): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }
        if ($raw[0] === "'" && preg_match("/^'([^']*)'/", $raw, $matches)) {
            return $matches[1];
        }
        if ($raw[0] === '"' && preg_match('/^"((?:[^"\\\\]|\\\\.)*)"/', $raw, $matches)) {
            return preg_replace_callback(
                '/\\\\(["\\\\$nrtfv])/',
                fn (array $escape) => match ($escape[1]) {
                    'n' => "\n",
                    'r' => "\r",
                    't' => "\t",
                    'f' => "\f",
                    'v' => "\v",
                    default => $escape[1],
                },
                $matches[1],
            );
        }
        return trim(preg_replace('/\s+#.*$/', '', $raw));
    }
    private function findLine(array $lines, string $key, bool $commented): ?int
    {
        $pattern = $commented
            ? '/^\s*#\s*'.preg_quote($key, '/').'\s*=/'
            : '/^\s*'.preg_quote($key, '/').'\s*=/';
        foreach ($lines as $index => $line) {
            if (preg_match($pattern, $line)) {
                return $index;
            }
        }
        return null;
    }
    private function write(array $lines): void
    {
        $contents = rtrim(implode(PHP_EOL, $lines)).PHP_EOL;
        if (@file_put_contents($this->path, $contents, LOCK_EX) === false) {
            throw new \RuntimeException('.env dosyasına yazılamadı. Dosya izinlerini kontrol edin.');
        }
        $this->clearConfigCache();
    }
}
