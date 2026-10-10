<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@admin.local'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        Site::updateOrCreate(
            ['name' => 'Örnek Sitesi'],
            [
                'name' => 'Örnek Sitesi',
                'address' => 'Örnek Mahallesi, No: 1',
            ]
        );
    }
}
