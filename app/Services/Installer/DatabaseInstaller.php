<?php
namespace App\Services\Installer;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseInstaller
{
    private const CONNECTION = 'installer';
    private const SERVER_CONNECTION = 'installer_server';
    public function __construct(private readonly EnvironmentWriter $env) {}
    public function connectionConfig(array $data): array
    {
        if ($data['driver'] === 'sqlite') {
            return [
                'driver' => 'sqlite',
                'database' => database_path('database.sqlite'),
                'prefix' => '',
                'foreign_key_constraints' => true,
            ];
        }
        return [
            'driver' => $data['driver'],
            'host' => $data['host'],
            'port' => (string) $data['port'],
            'database' => $data['database'],
            'username' => $data['username'],
            'password' => (string) ($data['password'] ?? ''),
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ];
    }
    public function connect(array $data, bool $createDatabase = false): Connection
    {
        $config = $this->connectionConfig($data);
        if ($data['driver'] === 'sqlite') {
            if (! is_file($config['database']) && @touch($config['database']) === false) {
                throw new \RuntimeException('SQLite dosyası oluşturulamadı: database/database.sqlite. "database" klasörüne yazma izni verin.');
            }
        } elseif ($createDatabase) {
            $this->createDatabase($config);
        }
        config(['database.connections.'.self::CONNECTION => $config]);
        DB::purge(self::CONNECTION);
        $connection = DB::connection(self::CONNECTION);
        $connection->getPdo();
        return $connection;
    }
    public function inspect(array $data, bool $createDatabase = false): array
    {
        $connection = $this->connect($data, $createDatabase);
        $schema = Schema::connection(self::CONNECTION);
        $tables = array_values(array_diff($schema->getTableListing(), ['migrations']));
        $hasUsers = $schema->hasTable('users') && $connection->table('users')->exists();
        return [
            'version' => (string) $connection->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION),
            'tables' => count($tables),
            'has_users' => $hasUsers,
        ];
    }
    public function migrate(array $data, bool $fresh = false): string
    {
        $this->connect($data);
        Artisan::call($fresh ? 'migrate:fresh' : 'migrate', [
            '--database' => self::CONNECTION,
            '--force' => true,
        ]);
        return trim(Artisan::output());
    }
    public function persist(array $data): void
    {
        if ($data['driver'] === 'sqlite') {
            $this->env->set(['DB_CONNECTION' => 'sqlite']);
            $this->env->forget(['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD']);
            return;
        }
        $this->env->set([
            'DB_CONNECTION' => $data['driver'],
            'DB_HOST' => $data['host'],
            'DB_PORT' => (string) $data['port'],
            'DB_DATABASE' => $data['database'],
            'DB_USERNAME' => $data['username'],
            'DB_PASSWORD' => (string) ($data['password'] ?? ''),
        ]);
    }
    private function createDatabase(array $config): void
    {
        $name = $config['database'];
        $server = $config;
        $server['database'] = null;
        config(['database.connections.'.self::SERVER_CONNECTION => $server]);
        DB::purge(self::SERVER_CONNECTION);
        DB::connection(self::SERVER_CONNECTION)->statement(
            'CREATE DATABASE IF NOT EXISTS `'.str_replace('`', '', $name).'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
        DB::purge(self::SERVER_CONNECTION);
    }
}
