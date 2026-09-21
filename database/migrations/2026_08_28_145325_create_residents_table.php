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
        Schema::create('residents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('apartment_id')->index('residents_apartment_id_foreign');
            $table->string('full_name', 160);
            $table->string('phone', 30)->nullable();
            $table->string('email', 160)->nullable();
            $table->string('resident_type', 20)->default('owner');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
