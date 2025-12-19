<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount_usd',
        'rate',
        'amount_cop',
        'counterparty',
        'method',
        'profit_cop',
        'transacted_at',
        'reference',
    ];

    protected $casts = [
        'transacted_at' => 'date',
    ];
}
