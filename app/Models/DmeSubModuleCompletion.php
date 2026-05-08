<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DmeSubModuleCompletion extends Model
{
    protected $fillable = ['dme_sub_module_id', 'batch'];

    public function subModule()
    {
        return $this->belongsTo(DmeSubModule::class, 'dme_sub_module_id');
    }
}
