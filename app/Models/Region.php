<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name', 'slug'];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function regionalRates()
    {
        return $this->hasMany(RegionalRate::class);
    }
}
