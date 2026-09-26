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
            $table->increments('id');
            $table->integer('site_id');
            $table->string('provider')->default('vakifbank');
            $table->string('environment')->default('test');
            $table->string('customer_no')->nullable();
            $table->string('account_no')->nullable();
            $table->string('iban')->nullable();
            $table->string('corporate_username')->nullable();
            $table->text('corporate_password')->nullable();
            $table->string('service_url')->nullable();
            $table->integer('sync_interval_minutes')->default(5);
            $table->dateTime('last_synced_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->text('options')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

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
