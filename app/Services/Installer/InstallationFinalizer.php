<?php
namespace App\Services\Installer;
use App\Models\Apartment;
use App\Models\AuditLog;
use App\Models\BuildingBlock;
use App\Models\Site;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
class InstallationFinalizer
{
    public function __construct(
        private readonly EnvironmentWriter $env,
        private readonly InstallationState $state,
    ) {}
    public function finalize(array $data, ?string $ip): array
    {
        $this->env->set($this->environmentValues($data));
        $result = DB::transaction(function () use ($data, $ip) {
            $admin = $this->createAdmin($data['admin']);
            $site = $this->createSite($data['site']);
            $this->storeSettings($data);
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'system_install',
                'table_name' => 'system',
                'ip_address' => $ip,
                'description' => 'Kurulum sihirbazı tamamlandı.',
            ]);
            return [
                'admin_email' => $admin->email,
                'site_name' => $site['name'],
                'blocks' => $site['blocks'],
                'apartments' => $site['apartments'],
            ];
        });
        $result['lock_written'] = $this->state->writeLock();
        return $result;
    }
    public function environmentValues(array $data): array
    {
        $app = $data['application'];
        $mail = $data['communication']['mail'];
        $values = [
            'APP_URL' => rtrim($app['app_url'], '/'),
            'APP_ENV' => $app['app_env'],
            'APP_DEBUG' => (bool) $app['app_debug'],
            'APP_TIMEZONE' => $app['timezone'],
            'MAIL_MAILER' => $mail['mailer'],
            'MAIL_FROM_ADDRESS' => $mail['from_address'],
            'MAIL_FROM_NAME' => $mail['from_name'],
        ];
        if ($mail['mailer'] === 'smtp') {
            $values += [
                'MAIL_HOST' => $mail['host'],
                'MAIL_PORT' => (string) $mail['port'],
                'MAIL_SCHEME' => $mail['scheme'] === 'smtps' ? 'smtps' : null,
                'MAIL_USERNAME' => $mail['username'] !== '' ? $mail['username'] : null,
                'MAIL_PASSWORD' => ! empty($mail['password']) ? Crypt::decryptString($mail['password']) : null,
            ];
        }
        return $values;
    }
    private function createAdmin(array $admin): User
    {
        return User::updateOrCreate(
            ['email' => strtolower($admin['email'])],
            [
                'name' => $admin['name'],
                'password' => Crypt::decryptString($admin['password']),
                'role' => 'admin',
                'site_id' => null,
                'is_active' => true,
                'security_question' => $admin['security_question'],
                'security_answer' => trim($admin['security_answer']),
                'failed_attempts' => 0,
                'locked_until' => null,
            ],
        );
    }
    private function createSite(array $data): array
    {
        $site = Site::updateOrCreate(
            ['name' => $data['name']],
            [
                'address' => $data['address'] ?: null,
                'opening_balance' => $data['opening_balance'],
                'opening_balance_date' => $data['opening_balance_date'],
            ],
        );
        $perFloor = (int) ($data['per_floor'] ?? 0);
        $apartments = 0;
        $now = now();
        foreach ($data['blocks'] as $blockData) {
            $block = BuildingBlock::firstOrCreate(['site_id' => $site->id, 'name' => $blockData['name']]);
            $rows = [];
            for ($number = 1; $number <= (int) $blockData['count']; $number++) {
                $rows[] = [
                    'building_block_id' => $block->id,
                    'number' => (string) $number,
                    'floor_no' => $perFloor > 0 ? intdiv($number - 1, $perFloor) + 1 : null,
                    'status' => 'occupied',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            foreach (array_chunk($rows, 200) as $chunk) {
                Apartment::query()->insertOrIgnore($chunk);
            }
            $apartments += count($rows);
        }
        return [
            'name' => $site->name,
            'blocks' => count($data['blocks']),
            'apartments' => $apartments,
        ];
    }
    private function storeSettings(array $data): void
    {
        $communication = $data['communication'];
        $visual = $data['visual'];
        $whatsapp = $communication['whatsapp'];
        SystemSetting::setValue('general', 'public_url', $communication['public_url'] ?: rtrim($data['application']['app_url'], '/'));
        SystemSetting::setValue('general', 'support_email', $communication['support_email'] ?: null);
        SystemSetting::setValue('whatsapp', 'api_url', $whatsapp['api_url'] ?: null);
        SystemSetting::setValue('whatsapp', 'session_id', $whatsapp['session_id'] ?: null);
        SystemSetting::setValue('whatsapp', 'group_id', $whatsapp['group_id'] ?: null);
        SystemSetting::setValue('whatsapp', 'enabled', $whatsapp['enabled'] ? '1' : '0');
        if (! empty($whatsapp['token'])) {
            SystemSetting::setValue('whatsapp', 'token', Crypt::decryptString($whatsapp['token']), true);
        }
        SystemSetting::setValue('ui_effects', 'accent_color', $visual['accent_color']);
        SystemSetting::setValue('ui_effects', 'glass_effect', $visual['glass_effect'] ? '1' : '0');
        SystemSetting::setValue('ui_effects', 'animations_effect', $visual['animations_effect'] ? '1' : '0');
        SystemSetting::setValue('ui_effects', 'card_hover_effect', $visual['card_hover_effect'] ? '1' : '0');
        SystemSetting::setValue('bubble_effects', 'enabled', $visual['enabled'] ? '1' : '0');
        SystemSetting::setValue('bubble_effects', 'count', (string) $visual['count']);
        SystemSetting::setValue('bubble_effects', 'speed', $visual['speed']);
        SystemSetting::setValue('bubble_effects', 'opacity', (string) $visual['opacity']);
        SystemSetting::setValue('bubble_effects', 'color_mode', $visual['color_mode']);
        SystemSetting::setValue('bubble_effects', 'size', $visual['size']);
    }
}
