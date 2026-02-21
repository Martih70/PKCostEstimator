<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectElementCost extends Model
{
    protected $fillable = ['project_id', 'estimating_element_id', 'total_cost', 'gross_floor_area', 'rate_per_m2', 'calculated_at'];

    protected $casts = [
        'total_cost' => 'decimal:2',
        'gross_floor_area' => 'decimal:2',
        'rate_per_m2' => 'decimal:2',
        'calculated_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function estimatingElement()
    {
        return $this->belongsTo(EstimatingElement::class);
    }
}
