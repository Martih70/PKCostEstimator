<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['project_id', 'project_nr', 'unique_id', 'project_type', 'name', 'location', 'city_area', 'region_id', 'gross_floor_area', 'budget_cost', 'notes', 'exclude_from_estimator', 'is_overhead_centre'];

    protected $casts = [
        'gross_floor_area' => 'decimal:2',
        'budget_cost' => 'decimal:2',
        'exclude_from_estimator' => 'boolean',
        'is_overhead_centre' => 'boolean',
    ];

    public function getProjectNumberAttribute()
    {
        return $this->project_nr;
    }

    public function getGfaAttribute()
    {
        return $this->gross_floor_area;
    }

    public function getExcludedFromEstimateAttribute()
    {
        return $this->exclude_from_estimator;
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function projectElementCosts()
    {
        return $this->hasMany(ProjectElementCost::class);
    }
}
