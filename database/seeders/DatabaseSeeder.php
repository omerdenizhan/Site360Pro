<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Site; // Site modelini ekleyin
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin kullanıcısı
        User::updateOrCreate(
            ['email' => 'admin@admin.local'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Örnek Site kaydı ekleyelim
        Site::updateOrCreate(
            ['name' => 'Merkez Site'], // Veya benzersiz bir sütun
            [
                'name' => 'Merkez Site',
                'address' => 'Örnek Mahallesi, No: 1',
                // Tablonuzdaki diğer zorunlu alanlar varsa buraya ekleyebilirsiniz
            ]
        );
    }
}
