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
            $table->foreign(['bank_integration_id'])->references(['id'])->on('bank_integrations')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['matched_due_id'])->references(['id'])->on('dues')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['matched_payment_id'])->references(['id'])->on('payments')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['site_id'])->references(['id'])->on('sites')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_transactions', function (Blueprint $table) {
            $table->dropForeign('bank_transactions_bank_integration_id_foreign');
            $table->dropForeign('bank_transactions_matched_due_id_foreign');
            $table->dropForeign('bank_transactions_matched_payment_id_foreign');
            $table->dropForeign('bank_transactions_site_id_foreign');
        });
    }
};
