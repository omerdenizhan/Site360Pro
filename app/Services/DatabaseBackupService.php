<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseBackupService
{
    public const FORMAT = 'site360pro-database-backup-v1';

    private const SKIPPED = ['migrations', 'sessions', 'cache', 'cache_locks'];

    public function driver(): string
    {
        return DB::connection()->getDriverName();
    }

    public function tables(): array
    {
        $rows = match ($this->driver()) {
            'sqlite' => DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"),
            'mysql', 'mariadb' => DB::select('SHOW TABLES'),
            'pgsql' => DB::select("SELECT tablename AS name FROM pg_tables WHERE schemaname = 'public'"),
            default => throw new \RuntimeException('Bu veritabanı sürücüsü desteklenmiyor.'),
        };

        return array_values(array_filter(array_map(fn ($row) => array_values((array) $row)[0] ?? null, $rows)));
    }

    public function dataTables(): array
    {
        return array_values(array_diff($this->tables(), self::SKIPPED));
    }

    public function overview(): array
    {
        try {
            $tables = $this->dataTables();
            $rows = 0;
            foreach ($tables as $table) {
                $rows += DB::table($table)->count();
            }

            return ['driver' => $this->driver(), 'table_count' => count($tables), 'row_count' => $rows];
        } catch (\Throwable $e) {
            return ['driver' => $this->driver(), 'table_count' => 0, 'row_count' => 0];
        }
    }

    public function snapshot(): array
    {
        $data = [];
        foreach ($this->dataTables() as $table) {
            $data[$table] = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
        }

        return [
            'format' => self::FORMAT,
            'created_at' => now()->toIso8601String(),
            'driver' => $this->driver(),
            'tables' => $data,
        ];
    }

    public function toSql(array $snapshot): string
    {
        $pdo = DB::connection()->getPdo();
        $sql = "-- Site360Pro data backup\n-- Created: ".$snapshot['created_at']."\n\n";

        foreach ($snapshot['tables'] as $table => $rows) {
            foreach ($rows as $row) {
                if (! $row) {
                    continue;
                }
                $columns = array_map(fn ($c) => '`'.str_replace('`', '``', $c).'`', array_keys($row));
                $values = array_map(fn ($v) => $v === null ? 'NULL' : $pdo->quote((string) $v), array_values($row));
                $sql .= 'INSERT INTO `'.str_replace('`', '``', $table).'` ('.implode(', ', $columns).') VALUES ('.implode(', ', $values).");\n";
            }
        }

        return $sql;
    }

    public function isValidBackup(mixed $backup): bool
    {
        return is_array($backup) && ($backup['format'] ?? null) === self::FORMAT && is_array($backup['tables'] ?? null);
    }

    public function restore(array $backup): void
    {
        $this->withoutForeignKeys(function () use ($backup) {
            DB::transaction(function () use ($backup) {
                foreach ($backup['tables'] as $table => $rows) {
                    if (! is_string($table) || in_array($table, self::SKIPPED, true) || ! Schema::hasTable($table) || ! is_array($rows)) {
                        continue;
                    }
                    DB::table($table)->delete();
                    foreach (array_chunk($rows, 250) as $chunk) {
                        if ($chunk) {
                            DB::table($table)->insert($chunk);
                        }
                    }
                }
            });
        });
    }

    public function wipe(): void
    {
        $this->withoutForeignKeys(function () {
            foreach (array_diff($this->tables(), ['migrations']) as $table) {
                DB::table($table)->delete();
            }
        });
    }

    private function withoutForeignKeys(callable $callback): void
    {
        $driver = $this->driver();

        try {
            match (true) {
                $driver === 'sqlite' => DB::statement('PRAGMA foreign_keys = OFF'),
                in_array($driver, ['mysql', 'mariadb'], true) => DB::statement('SET FOREIGN_KEY_CHECKS=0'),
                $driver === 'pgsql' => DB::statement('SET session_replication_role = replica'),
                default => null,
            };
            $callback();
        } finally {
            match (true) {
                $driver === 'sqlite' => DB::statement('PRAGMA foreign_keys = ON'),
                in_array($driver, ['mysql', 'mariadb'], true) => DB::statement('SET FOREIGN_KEY_CHECKS=1'),
                $driver === 'pgsql' => DB::statement('SET session_replication_role = DEFAULT'),
                default => null,
            };
        }
    }
}
