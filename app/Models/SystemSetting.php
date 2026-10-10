<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\ComponentAttributeBag;

class SystemSetting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'is_encrypted'];

    protected function casts(): array
    {
        return [
            'is_encrypted' => 'boolean',
        ];
    }

    public static function getValue(string $group, string $key, ?string $default = null): ?string
    {
        $setting = self::query()->where('group', $group)->where('key', $key)->first();

        if (! $setting || $setting->value === null) {
            return $default;
        }

        return $setting->is_encrypted ? Crypt::decryptString($setting->value) : $setting->value;
    }

    public static function setValue(string $group, string $key, ?string $value, bool $encrypted = false): void
    {
        self::query()->updateOrCreate(
            ['group' => $group, 'key' => $key],
            [
                'value' => $value === null || $value === '' ? null : ($encrypted ? Crypt::encryptString($value) : $value),
                'is_encrypted' => $encrypted,
            ],
        );
    }

    public static function uiEffects(): array
    {
        static $cache = null;

        if ($cache !== null) {
            return $cache;
        }

        try {
            $accent = (string) self::getValue('ui_effects', 'accent_color', '#206bc4');

            $cache = [
                'accent' => preg_match('/^#[0-9a-fA-F]{6}$/', $accent) ? $accent : '#206bc4',
                'glass' => self::getValue('ui_effects', 'glass_effect', '0') === '1',
                'anim' => self::getValue('ui_effects', 'animations_effect', '1') === '1',
                'hover' => self::getValue('ui_effects', 'card_hover_effect', '1') === '1',
                'bubble' => [
                    'enabled' => self::getValue('bubble_effects', 'enabled', '0') === '1',
                    'count' => (int) self::getValue('bubble_effects', 'count', '20'),
                    'speed' => self::getValue('bubble_effects', 'speed', 'normal'),
                    'opacity' => (int) self::getValue('bubble_effects', 'opacity', '30'),
                    'color' => self::getValue('bubble_effects', 'color_mode', 'accent'),
                    'size' => self::getValue('bubble_effects', 'size', 'mixed'),
                ],
            ];
        } catch (\Throwable $e) {
            $cache = [
                'accent' => '#206bc4', 'glass' => false, 'anim' => true, 'hover' => true,
                'bubble' => ['enabled' => false, 'count' => 20, 'speed' => 'normal', 'opacity' => 30, 'color' => 'accent', 'size' => 'mixed'],
            ];
        }

        return $cache;
    }

    public static function htmlAttributes(): ComponentAttributeBag
    {
        $fx = self::uiEffects();
        $accent = $fx['accent'];
        [$r, $g, $b] = [hexdec(substr($accent, 1, 2)), hexdec(substr($accent, 3, 2)), hexdec(substr($accent, 5, 2))];
        $dark = sprintf('#%02x%02x%02x', (int) ($r * .75), (int) ($g * .75), (int) ($b * .75));

        return new ComponentAttributeBag([
            'data-fx' => '1',
            'data-fx-glass' => $fx['glass'] ? '1' : '0',
            'data-fx-anim' => $fx['anim'] ? '1' : '0',
            'data-fx-hover' => $fx['hover'] ? '1' : '0',
            'data-fx-bubble' => ! empty($fx['bubble']['enabled']) ? '1' : '0',
            'style' => "--fx-accent: {$accent}; --fx-rgb: {$r}, {$g}, {$b}; --fx-accent-dark: {$dark};",
        ]);
    }
}
