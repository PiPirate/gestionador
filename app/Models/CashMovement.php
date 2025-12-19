<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'type',
        'description',
        'amount_cop',
        'balance_cop',
        'reference',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
