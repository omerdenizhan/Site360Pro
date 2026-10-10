<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dues', function (Blueprint $table) {
            $table->foreign(['apartment_id'], null)->references(['id'])->on('apartments')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('dues', function (Blueprint $table) {
            $table->dropForeign();
        });
    }
};
