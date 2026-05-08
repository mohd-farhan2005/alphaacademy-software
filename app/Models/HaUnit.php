<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HaUnit extends Model
{
    protected $fillable = ['ha_module_id', 'name'];

    public function module()
    {
        return $this->belongsTo(HaModule::class, 'ha_module_id');
    }

    public function completions()
    {
        return $this->hasMany(HaUnitCompletion::class);
    }
}
