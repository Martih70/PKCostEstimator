<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstimatingElement extends Model
{
    protected $fillable = ['code', 'name', 'sort_order'];

    public function pdCodes()
    {
        return $this->hasMany(PdCode::class);
    }

    public function projectElementCosts()
    {
        return $this->hasMany(ProjectElementCost::class);
    }

    public function regionalRates()
    {
        return $this->hasMany(RegionalRate::class);
    }
}
