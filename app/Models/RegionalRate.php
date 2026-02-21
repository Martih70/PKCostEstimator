<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegionalRate extends Model
{
    protected $fillable = ['region_id', 'estimating_element_id', 'low_rate', 'medium_rate', 'high_rate', 'high_plus_rate', 'project_count', 'min_project_nr', 'max_project_nr', 'data_from_date', 'data_to_date', 'calculated_at'];

    protected $casts = [
        'low_rate' => 'decimal:2',
        'medium_rate' => 'decimal:2',
        'high_rate' => 'decimal:2',
        'high_plus_rate' => 'decimal:2',
        'data_from_date' => 'date',
        'data_to_date' => 'date',
        'calculated_at' => 'datetime',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function estimatingElement()
    {
        return $this->belongsTo(EstimatingElement::class);
    }
}
