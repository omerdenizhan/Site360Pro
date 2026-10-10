<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = [
        'workplace',
        'vehicle_model_1',
        'vehicle_plate_1',
        'vehicle_model_2',
        'vehicle_plate_2',
    ];

    public function up(): void
    {
        foreach ($this->columns as $column) {
            if (! Schema::hasColumn('residents', $column)) {
                Schema::table('residents', function (Blueprint $table) use ($column) {
                    $table->string($column)->nullable()->after('resident_type');
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->columns as $column) {
            if (Schema::hasColumn('residents', $column)) {
                Schema::table('residents', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
