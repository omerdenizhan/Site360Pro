<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\RedirectResponse;

class DatabaseSettingsController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403, 'Bu işlem yalnızca genel yöneticiler tarafından yapılabilir.');
    }

    private function tables(): array
    {
        $driver = DB::connection()->getDriverName();
        $rows = match ($driver) {
            'sqlite' => DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'"),
            'mysql', 'mariadb' => DB::select('SHOW TABLES'),
            'pgsql' => DB::select("SELECT tablename AS name FROM pg_tables WHERE schemaname = 'public'"),
            default => throw new \RuntimeException('Bu veritabanı sürücüsü desteklenmiyor.'),
        };
        return array_values(array_filter(array_map(fn ($row) => (array_values((array) $row))[0] ?? null, $rows)));
    }

    private function snapshot(): array
    {
        $data = [];
        foreach ($this->tables() as $table) {
            $data[$table] = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
        }
        return ['format' => 'site360pro-database-backup-v1', 'created_at' => now()->toIso8601String(), 'driver' => DB::connection()->getDriverName(), 'tables' => $data];
    }

    public function json(Request $request)
    {
        $this->authorizeAdmin($request);
        $json = json_encode($this->snapshot(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return response($json, 200, ['Content-Type' => 'application/json', 'Content-Disposition' => 'attachment; filename="site360pro-backup-'.now()->format('Ymd-His').'.json"']);
    }

    public function sql(Request $request)
    {
        $this->authorizeAdmin($request);
        $snapshot = $this->snapshot();
        $sql = "-- Site360Pro data backup\n-- Created: ".$snapshot['created_at']."\n\n";
        foreach ($snapshot['tables'] as $table => $rows) {
            foreach ($rows as $row) {
                if (!$row) continue;
                $columns = array_map(fn ($c) => '`'.str_replace('`', '``', $c).'`', array_keys($row));
                $values = array_map(fn ($v) => $v === null ? 'NULL' : DB::connection()->getPdo()->quote((string) $v), array_values($row));
                $sql .= 'INSERT INTO `'.str_replace('`', '``', $table).'` ('.implode(', ', $columns).') VALUES ('.implode(', ', $values).");\n";
            }
        }
        return response($sql, 200, ['Content-Type' => 'application/sql', 'Content-Disposition' => 'attachment; filename="site360pro-backup-'.now()->format('Ymd-His').'.sql"']);
    }

    public function restore(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $request->validate(['backup' => ['required', 'file', 'max:51200'], 'confirmation' => ['required', 'in:GERI_YUKLE']]);
        $contents = file_get_contents($request->file('backup')->getRealPath());
        try {
            $backup = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            abort_unless(($backup['format'] ?? null) === 'site360pro-database-backup-v1' && is_array($backup['tables'] ?? null), 422, 'Yedek dosyası geçerli değil. JSON formatındaki Site360Pro yedeğini seçin.');
            DB::transaction(function () use ($backup) {
                foreach ($backup['tables'] as $table => $rows) {
                    if (!is_string($table) || !Schema::hasTable($table) || !is_array($rows)) continue;
                    DB::table($table)->delete();
                    foreach (array_chunk($rows, 250) as $chunk) if ($chunk) DB::table($table)->insert($chunk);
                }
            });
        } catch (\JsonException $e) {
            return back()->withErrors(['backup' => 'Bu sürümde geri yükleme için geçerli Site360Pro JSON yedeği gerekir. SQL dosyası doğrudan yüklenemez.']);
        }
        AuditLog::create(['user_id' => $request->user()->id, 'action' => 'database_restore', 'table_name' => 'multiple', 'ip_address' => $request->ip(), 'description' => 'Veritabanı JSON yedeğinden geri yüklendi.']);
        return back()->with('status', 'Veritabanı yedekten geri yüklendi.');
    }

    public function wipe(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $request->validate(['confirmation' => ['required', 'in:VERITABANINI_SIL']]);
        $tables = array_values(array_diff($this->tables(), ['migrations']));
        $driver = DB::connection()->getDriverName();
        try {
            if ($driver === 'sqlite') DB::statement('PRAGMA foreign_keys = OFF');
            elseif (in_array($driver, ['mysql', 'mariadb'])) DB::statement('SET FOREIGN_KEY_CHECKS=0');
            elseif ($driver === 'pgsql') DB::statement('SET session_replication_role = replica');
            foreach ($tables as $table) DB::table($table)->delete();
        } finally {
            if ($driver === 'sqlite') DB::statement('PRAGMA foreign_keys = ON');
            elseif (in_array($driver, ['mysql', 'mariadb'])) DB::statement('SET FOREIGN_KEY_CHECKS=1');
            elseif ($driver === 'pgsql') DB::statement('SET session_replication_role = DEFAULT');
        }
        return back()->with('status', 'Tablolardaki kayıtlar silindi. Veritabanı şeması ve migration geçmişi korundu.');
    }
}
