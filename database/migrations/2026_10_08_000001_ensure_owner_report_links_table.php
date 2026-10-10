<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('owner_report_links')) {
            return;
        }

        Schema::create('owner_report_links', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('apartment_id')->index();
            $table->string('token')->unique();
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('last_used_at')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('apartment_id')->references('id')->on('apartments')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('restrict')->onDelete('set null');
        });
    }

    public function down(): void {}
};
