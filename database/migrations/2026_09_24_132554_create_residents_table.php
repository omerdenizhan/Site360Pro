<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('apartment_id')->index('residents_apartment_id_foreign');
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('resident_type')->default('owner');
            $table->string('workplace')->nullable();
            $table->string('vehicle_model_1')->nullable();
            $table->string('vehicle_plate_1')->nullable();
            $table->string('vehicle_model_2')->nullable();
            $table->string('vehicle_plate_2')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
