<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bank_integrations', function (Blueprint $table) {
            $table->foreign(['site_id'], null)->references(['id'])->on('sites')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('bank_integrations', function (Blueprint $table) {
            $table->dropForeign();
        });
    }
};
