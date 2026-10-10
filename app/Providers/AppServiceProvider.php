<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if (config('database.default') === 'sqlite') {

            $pdo = DB::connection()->getPdo();

            $pdo->sqliteCreateFunction('MONTH', function ($date) {
                return $date ? (int) date('m', strtotime($date)) : null;
            });

            $pdo->sqliteCreateFunction('YEAR', function ($date) {
                return $date ? (int) date('Y', strtotime($date)) : null;
            });

            $pdo->sqliteCreateFunction('DAY', function ($date) {
                return $date ? (int) date('d', strtotime($date)) : null;
            });
        }

        $this->ensureSiteOpeningBalanceColumns();
    }

    private function ensureSiteOpeningBalanceColumns(): void
    {
        try {
            if (! Schema::hasTable('sites')) {
                return;
            }

            if (! Schema::hasColumn('sites', 'opening_balance')) {
                Schema::table('sites', fn (Blueprint $table) => $table->decimal('opening_balance', 14, 2)->default(0));
            }

            if (! Schema::hasColumn('sites', 'opening_balance_date')) {
                Schema::table('sites', fn (Blueprint $table) => $table->date('opening_balance_date')->nullable());
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
