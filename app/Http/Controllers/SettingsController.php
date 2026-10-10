<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SystemSetting;
use App\Services\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request, DatabaseBackupService $backups): View
    {
        $validTabs = ['general', 'whatsapp', 'visual', 'database'];
        $tab = $request->query('tab', 'general');
        if (! in_array($tab, $validTabs, true)) {
            $tab = 'general';
        }

        $isAdmin = $request->user()?->role === 'admin';

        return view('settings.index', [
            'tab' => $tab,
            'isAdmin' => $isAdmin,
            'database' => $tab === 'database' && $isAdmin ? $backups->overview() : null,
            'settings' => [

                'app_public_url' => SystemSetting::getValue('general', 'public_url', config('app.url')),
                'support_email' => SystemSetting::getValue('general', 'support_email'),

                'whatsapp_api_url' => SystemSetting::getValue('whatsapp', 'api_url', ''),
                'whatsapp_session_id' => SystemSetting::getValue('whatsapp', 'session_id', ''),
                'whatsapp_token_masked' => SystemSetting::getValue('whatsapp', 'token') ? '••••••••••••••••' : null,
                'whatsapp_group_id' => SystemSetting::getValue('whatsapp', 'group_id', ''),
                'whatsapp_enabled' => SystemSetting::getValue('whatsapp', 'enabled', '0'),

                'accent_color' => SystemSetting::getValue('ui_effects', 'accent_color', '#206bc4'),
                'glass_effect' => SystemSetting::getValue('ui_effects', 'glass_effect', '0'),
                'animations_effect' => SystemSetting::getValue('ui_effects', 'animations_effect', '1'),
                'card_hover_effect' => SystemSetting::getValue('ui_effects', 'card_hover_effect', '1'),

                'bubble_enabled' => SystemSetting::getValue('bubble_effects', 'enabled', '0'),
                'bubble_count' => SystemSetting::getValue('bubble_effects', 'count', '20'),
                'bubble_speed' => SystemSetting::getValue('bubble_effects', 'speed', 'normal'),
                'bubble_opacity' => SystemSetting::getValue('bubble_effects', 'opacity', '30'),
                'bubble_color_mode' => SystemSetting::getValue('bubble_effects', 'color_mode', 'accent'),
                'bubble_size' => SystemSetting::getValue('bubble_effects', 'size', 'mixed'),
            ],
        ]);
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'public_url' => ['nullable', 'url', 'max:255'],
            'support_email' => ['nullable', 'email', 'max:160'],
        ]);

        SystemSetting::setValue('general', 'public_url', $validated['public_url'] ?? null);
        SystemSetting::setValue('general', 'support_email', $validated['support_email'] ?? null);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_general_settings',
            'table_name' => 'system_settings',
            'ip_address' => $request->ip(),
            'description' => 'Genel sistem ayarları güncellendi.',
        ]);

        return redirect()->route('settings.index', ['tab' => 'general'])->with('status', 'Genel ayarlar kaydedildi.');
    }

    public function updateWhatsapp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'api_url' => ['nullable', 'string', 'max:255'],
            'session_id' => ['nullable', 'string', 'max:100'],
            'token' => ['nullable', 'string', 'max:255'],
            'group_id' => ['nullable', 'string', 'max:120'],
            'enabled' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('enabled')) {
            $missing = [];
            foreach (['api_url' => 'API URL', 'session_id' => 'Oturum / Cihaz Kimliği', 'group_id' => 'Group ID'] as $field => $label) {
                if (trim((string) ($validated[$field] ?? '')) === '') {
                    $missing[] = $label;
                }
            }
            if (trim((string) ($validated['token'] ?? '')) === '' && ! SystemSetting::getValue('whatsapp', 'token')) {
                $missing[] = 'API Token';
            }

            if ($missing !== []) {
                return redirect()
                    ->route('settings.index', ['tab' => 'whatsapp'])
                    ->withInput($request->except('token'))
                    ->withErrors(['whatsapp' => 'WhatsApp entegrasyonunu aktif etmek için şu alanları doldurun: '.implode(', ', $missing).'. Ayarlar kaydedilmedi.']);
            }
        }

        SystemSetting::setValue('whatsapp', 'api_url', $validated['api_url'] ?? null);
        SystemSetting::setValue('whatsapp', 'session_id', $validated['session_id'] ?? null);
        if (! empty($validated['token'])) {
            SystemSetting::setValue('whatsapp', 'token', $validated['token'], true);
        }
        SystemSetting::setValue('whatsapp', 'group_id', $validated['group_id'] ?? null);
        SystemSetting::setValue('whatsapp', 'enabled', $request->boolean('enabled') ? '1' : '0');

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_whatsapp_settings',
            'table_name' => 'system_settings',
            'ip_address' => $request->ip(),
            'description' => 'WhatsApp API ayarları güncellendi.',
        ]);

        return redirect()->route('settings.index', ['tab' => 'whatsapp'])->with('status', 'WhatsApp API ayarları kaydedildi.');
    }

    public function updateVisual(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'accent_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'glass_effect' => ['nullable', 'boolean'],
            'animations_effect' => ['nullable', 'boolean'],
            'card_hover_effect' => ['nullable', 'boolean'],
            'enabled' => ['nullable', 'boolean'],
            'count' => ['nullable', 'integer', 'min:5', 'max:100'],
            'speed' => ['nullable', 'string', 'in:slow,normal,fast'],
            'opacity' => ['nullable', 'integer', 'min:5', 'max:90'],
            'color_mode' => ['nullable', 'string', 'in:accent,blue,pastel,white'],
            'size' => ['nullable', 'string', 'in:small,medium,large,mixed'],
        ]);

        SystemSetting::setValue('ui_effects', 'accent_color', $validated['accent_color']);
        SystemSetting::setValue('ui_effects', 'glass_effect', $request->boolean('glass_effect') ? '1' : '0');
        SystemSetting::setValue('ui_effects', 'animations_effect', $request->boolean('animations_effect') ? '1' : '0');
        SystemSetting::setValue('ui_effects', 'card_hover_effect', $request->boolean('card_hover_effect') ? '1' : '0');

        SystemSetting::setValue('bubble_effects', 'enabled', $request->boolean('enabled') ? '1' : '0');
        SystemSetting::setValue('bubble_effects', 'count', (string) ($validated['count'] ?? 20));
        SystemSetting::setValue('bubble_effects', 'speed', $validated['speed'] ?? 'normal');
        SystemSetting::setValue('bubble_effects', 'opacity', (string) ($validated['opacity'] ?? 30));
        SystemSetting::setValue('bubble_effects', 'color_mode', $validated['color_mode'] ?? 'accent');
        SystemSetting::setValue('bubble_effects', 'size', $validated['size'] ?? 'mixed');

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_visual_settings',
            'table_name' => 'system_settings',
            'ip_address' => $request->ip(),
            'description' => 'Görsellik (arayüz ve baloncuk efektleri) ayarları güncellendi.',
        ]);

        return redirect()->route('settings.index', ['tab' => 'visual'])->with('status', 'Görsellik ayarları kaydedildi ve tüm sayfalara uygulandı.');
    }
}
