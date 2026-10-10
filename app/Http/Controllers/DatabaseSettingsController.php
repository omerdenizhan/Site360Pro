<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DatabaseSettingsController extends Controller
{
    public function __construct(private readonly DatabaseBackupService $backups) {}

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'admin', 403, 'Bu işlem yalnızca genel yöneticiler tarafından yapılabilir.');
    }

    private function download(string $content, string $mime, string $extension): Response
    {
        return response($content, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'attachment; filename="site360pro-backup-'.now()->format('Ymd-His').'.'.$extension.'"',
        ]);
    }

    public function json(Request $request): Response
    {
        $this->authorizeAdmin($request);

        return $this->download(
            json_encode($this->backups->snapshot(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'application/json',
            'json',
        );
    }

    public function sql(Request $request): Response
    {
        $this->authorizeAdmin($request);

        return $this->download($this->backups->toSql($this->backups->snapshot()), 'application/sql', 'sql');
    }

    public function restore(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $request->validate(['backup' => ['required', 'file', 'max:51200'], 'confirmation' => ['required', 'in:GERI_YUKLE']]);

        try {
            $backup = json_decode(file_get_contents($request->file('backup')->getRealPath()), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return back()->withErrors(['backup' => 'Bu sürümde geri yükleme için geçerli Site360Pro JSON yedeği gerekir. SQL dosyası doğrudan yüklenemez.']);
        }

        if (! $this->backups->isValidBackup($backup)) {
            return back()->withErrors(['backup' => 'Yedek dosyası geçerli değil. JSON formatındaki Site360Pro yedeğini seçin.']);
        }

        $this->backups->restore($backup);

        AuditLog::create(['user_id' => $request->user()->id, 'action' => 'database_restore', 'table_name' => 'multiple', 'ip_address' => $request->ip(), 'description' => 'Veritabanı JSON yedeğinden geri yüklendi.']);

        return back()->with('status', 'Veritabanı yedekten geri yüklendi.');
    }

    public function wipe(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $request->validate(['confirmation' => ['required', 'in:VERITABANINI_SIL']]);

        $this->backups->wipe();

        return back()->with('status', 'Tablolardaki kayıtlar silindi. Veritabanı şeması ve migration geçmişi korundu.');
    }
}
