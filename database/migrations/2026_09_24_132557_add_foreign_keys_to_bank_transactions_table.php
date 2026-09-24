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
        Schema::table('bank_transactions', function (Blueprint $table) {
            $table->foreign(['site_id'], null)->references(['id'])->on('sites')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['matched_payment_id'], null)->references(['id'])->on('payments')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['matched_due_id'], null)->references(['id'])->on('dues')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['bank_integration_id'], null)->references(['id'])->on('bank_integrations')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_transactions', function (Blueprint $table) {
            $table->dropForeign();
            $table->dropForeign();
            $table->dropForeign();
            $table->dropForeign();
        });
    }
};
