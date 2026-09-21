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
        Schema::create('owner_report_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('apartment_id')->index('owner_report_links_apartment_id_foreign');
            $table->string('token', 80)->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index('owner_report_links_created_by_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_report_links');
    }
};
