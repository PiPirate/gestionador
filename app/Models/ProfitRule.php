<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProfitRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'tiers_json',
        'is_active',
    ];

    protected $casts = [
        'tiers_json' => 'array',
        'is_active' => 'boolean',
    ];

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }
}
