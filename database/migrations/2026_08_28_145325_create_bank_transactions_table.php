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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bank_integration_id')->index('bank_transactions_bank_integration_id_foreign');
            $table->unsignedBigInteger('site_id');
            $table->string('provider', 40)->default('vakifbank');
            $table->string('bank_transaction_id', 120);
            $table->string('operation_no', 80)->nullable();
            $table->timestamp('transaction_date');
            $table->decimal('amount', 12);
            $table->string('direction', 1)->default('A');
            $table->string('sender_name')->nullable();
            $table->string('sender_iban', 34)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 30)->default('unmatched');
            $table->unsignedBigInteger('matched_due_id')->nullable()->index('bank_transactions_matched_due_id_foreign');
            $table->unsignedBigInteger('matched_payment_id')->nullable()->index('bank_transactions_matched_payment_id_foreign');
            $table->string('match_reason')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

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
