<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            if (! Schema::hasColumn('sites', 'opening_balance')) {
                $table->decimal('opening_balance', 14, 2)->default(0)->after('address');
            }
            if (! Schema::hasColumn('sites', 'opening_balance_date')) {
                $table->date('opening_balance_date')->nullable()->after('opening_balance');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['opening_balance', 'opening_balance_date']);
        });
    }
};
