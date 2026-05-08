<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DmeSubModule extends Model
{
    protected $fillable = ['dme_sub_unit_id', 'name'];

    public function subUnit()
    {
        return $this->belongsTo(DmeSubUnit::class, 'dme_sub_unit_id');
    }

    public function completions()
    {
        return $this->hasMany(DmeSubModuleCompletion::class, 'dme_sub_module_id');
    }
}
