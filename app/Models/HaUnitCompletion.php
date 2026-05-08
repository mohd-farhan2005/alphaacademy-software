<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HaUnitCompletion extends Model
{
    protected $fillable = ['ha_unit_id', 'batch'];

    public function unit()
    {
        return $this->belongsTo(HaUnit::class, 'ha_unit_id');
    }
}
