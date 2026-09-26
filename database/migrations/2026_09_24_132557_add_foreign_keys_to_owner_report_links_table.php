<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('owner_report_links', function (Blueprint $table) {
            $table->foreign(['created_by'], null)->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['apartment_id'], null)->references(['id'])->on('apartments')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owner_report_links', function (Blueprint $table) {
            $table->dropForeign();
            $table->dropForeign();
        });
    }
};
