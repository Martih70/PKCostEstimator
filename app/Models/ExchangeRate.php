<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = ['currency_code', 'rate_to_pkr', 'effective_date'];

    protected $casts = [
        'rate_to_pkr' => 'decimal:4',
        'effective_date' => 'date',
    ];
}
