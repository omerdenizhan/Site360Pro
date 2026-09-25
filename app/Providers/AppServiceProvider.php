<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use PDO;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // SQLite kullanıldığında MySQL tarih fonksiyonlarını simüle ediyoruz
        if (config('database.default') === 'sqlite') {
            /** @var PDO $pdo */
            $pdo = DB::connection()->getPdo();

            // MONTH() fonksiyonu
            $pdo->sqliteCreateFunction('MONTH', function ($date) {
                return $date ? (int) date('m', strtotime($date)) : null;
            });

            // YEAR() fonksiyonu
            $pdo->sqliteCreateFunction('YEAR', function ($date) {
                return $date ? (int) date('Y', strtotime($date)) : null;
            });

            // DAY() fonksiyonu
            $pdo->sqliteCreateFunction('DAY', function ($date) {
                return $date ? (int) date('d', strtotime($date)) : null;
            });
        }
    }
}
