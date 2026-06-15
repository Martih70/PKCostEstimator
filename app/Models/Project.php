<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['project_id', 'project_nr', 'unique_id', 'project_type', 'name', 'location', 'city_area', 'region_id', 'gross_floor_area', 'seating_capacity', 'budget_cost', 'cost_estimate', 'approved_at', 'approved_by', 'project_start_date', 'completion_date', 'area_source', 'area_verified', 'notes', 'exclude_from_estimator', 'is_overhead_centre'];

    protected $casts = [
        'gross_floor_area' => 'decimal:2',
        'budget_cost' => 'decimal:2',
        'cost_estimate' => 'decimal:2',
        'approved_at' => 'datetime',
        'exclude_from_estimator' => 'boolean',
        'is_overhead_centre' => 'boolean',
        'project_start_date' => 'date',
        'completion_date' => 'date',
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

    public function getCostPerSeatAttribute(): ?float
    {
        if ($this->cost_estimate && $this->seating_capacity) {
            return round($this->cost_estimate / $this->seating_capacity, 2);
        }
        return null;
    }

    /**
     * Workflow status for a forecast project, derived entirely from
     * existing fields — never stored separately, so it can't drift out
     * of sync with the data it describes:
     *   - pending: no cost estimate saved yet
     *   - report_completed: a cost estimate has been saved
     *   - approved: a Cost Manager/Admin has approved the forecast
     */
    public function getStatusAttribute(): string
    {
        if ($this->approved_at !== null) {
            return 'approved';
        }

        return $this->cost_estimate !== null ? 'report_completed' : 'pending';
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
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
