<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscalationRate extends Model
{
    protected $fillable = ['monthly_rate_percent', 'notes', 'updated_by'];

    protected $casts = [
        'monthly_rate_percent' => 'decimal:3',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
