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
        Schema::create('apartments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('building_block_id');
            $table->string('number', 40);
            $table->unsignedSmallInteger('floor_no')->nullable();
            $table->string('status', 20)->default('occupied');
            $table->timestamps();

            $table->unique(['building_block_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
