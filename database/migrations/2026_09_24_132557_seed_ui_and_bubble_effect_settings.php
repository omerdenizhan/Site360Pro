<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $defaults = [
        'ui_effects' => [
            'accent_color' => '#206bc4',
            'glass_effect' => '0',
            'animations_effect' => '1',
            'card_hover_effect' => '1',
        ],
        'bubble_effects' => [
            'enabled' => '0',
            'count' => '20',
            'speed' => 'normal',
            'opacity' => '30',
            'color_mode' => 'accent',
            'size' => 'mixed',
        ],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        $now = now();

        foreach ($this->defaults as $group => $items) {
            foreach ($items as $key => $value) {
                $exists = DB::table('system_settings')
                    ->where('group', $group)
                    ->where('key', $key)
                    ->exists();

                if (! $exists) {
                    DB::table('system_settings')->insert([
                        'group' => $group,
                        'key' => $key,
                        'value' => $value,
                        'is_encrypted' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        DB::table('system_settings')
            ->whereIn('group', array_keys($this->defaults))
            ->delete();
    }
};
