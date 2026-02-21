<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdCode extends Model
{
    protected $fillable = ['code', 'name', 'area_code', 'mh_sort_order', 'estimating_element_id', 'category', 'is_contractor'];

    protected $casts = [
        'is_contractor' => 'boolean',
    ];

    public function estimatingElement()
    {
        return $this->belongsTo(EstimatingElement::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
