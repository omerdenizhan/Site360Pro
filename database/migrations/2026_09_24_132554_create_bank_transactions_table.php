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
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bank_integration_id')->index('bank_transactions_bank_integration_id_foreign');
            $table->integer('site_id');
            $table->string('provider')->default('vakifbank');
            $table->string('bank_transaction_id');
            $table->string('operation_no')->nullable();
            $table->dateTime('transaction_date');
            $table->decimal('amount');
            $table->string('direction')->default('A');
            $table->string('sender_name')->nullable();
            $table->string('sender_iban')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('unmatched');
            $table->integer('matched_due_id')->nullable()->index('bank_transactions_matched_due_id_foreign');
            $table->integer('matched_payment_id')->nullable()->index('bank_transactions_matched_payment_id_foreign');
            $table->string('match_reason')->nullable();
            $table->text('failure_reason')->nullable();
            $table->text('raw_payload')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->unique(['provider', 'bank_transaction_id']);
            $table->index(['site_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
