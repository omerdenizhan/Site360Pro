<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $fillable = ['name', 'address', 'opening_balance', 'opening_balance_date'];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'opening_balance_date' => 'date',
    ];

    public function cashStart(): ?Carbon
    {
        return $this->opening_balance_date
            ? $this->opening_balance_date->copy()->startOfMonth()
            : null;
    }

    public function buildingBlocks(): HasMany
    {
        return $this->hasMany(BuildingBlock::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
