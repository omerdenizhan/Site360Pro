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
        Schema::create('dues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('apartment_id');
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->decimal('amount', 12);
            $table->date('due_date');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique(['apartment_id', 'period_year', 'period_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dues');
    }
};
