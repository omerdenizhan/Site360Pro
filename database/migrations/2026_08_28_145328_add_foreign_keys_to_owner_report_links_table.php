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
            $table->foreign(['apartment_id'])->references(['id'])->on('apartments')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owner_report_links', function (Blueprint $table) {
            $table->dropForeign('owner_report_links_apartment_id_foreign');
            $table->dropForeign('owner_report_links_created_by_foreign');
        });
    }
};
