<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['transaction_date', 'raw_pd_code', 'pd_code_id', 'raw_area_code', 'raw_ac_code_mh', 'project_id', 'raw_project_nr', 'item_description', 'amount', 'import_batch'];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function pdCode()
    {
        return $this->belongsTo(PdCode::class);
    }
}
