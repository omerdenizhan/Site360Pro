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
        Schema::create('bank_integrations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('site_id');
            $table->string('provider', 40)->default('vakifbank');
            $table->string('environment', 20)->default('test');
            $table->string('customer_no', 20)->nullable();
            $table->string('account_no', 20)->nullable();
            $table->string('iban', 34)->nullable();
            $table->string('corporate_username')->nullable();
            $table->text('corporate_password')->nullable();
            $table->string('service_url')->nullable();
            $table->unsignedSmallInteger('sync_interval_minutes')->default(5);
            $table->timestamp('last_synced_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->json('options')->nullable();
            $table->timestamps();

            $table->unique(['site_id', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_integrations');
    }
};
