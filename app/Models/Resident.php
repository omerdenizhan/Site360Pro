<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resident extends Model
{
    protected $fillable = [
        'apartment_id',
        'full_name',
        'phone',
        'email',
        'resident_type',
        'workplace',
        'vehicle_model_1',
        'vehicle_plate_1',
        'vehicle_model_2',
        'vehicle_plate_2',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }
}
