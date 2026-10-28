<?php
namespace App\Providers;
use App\Services\Installer\EnvironmentWriter;
use App\Services\Installer\InstallationState;
use App\Services\Installer\RequirementsChecker;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EnvironmentWriter::class, fn () => new EnvironmentWriter(
            base_path('.env'),
            base_path('.env.example'),
            base_path('bootstrap/cache/config.php'),
        ));
        $this->app->singleton(InstallationState::class);
        $this->app->singleton(RequirementsChecker::class, fn ($app) => new RequirementsChecker(
            base_path(),
            config('installer'),
            $app->make(EnvironmentWriter::class),
        ));
    }
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        $this->registerSqliteFunctions();
        $this->ensureSiteOpeningBalanceColumns();
    }
    private function registerSqliteFunctions(): void
    {
        if (config('database.default') !== 'sqlite') {
            return;
        }
        try {
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
        } catch (\Throwable $e) {
            report($e);
        }
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
