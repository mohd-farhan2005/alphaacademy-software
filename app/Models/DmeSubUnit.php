<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DmeSubUnit extends Model
{
    protected $fillable = ['name'];

    public function subModules()
    {
        return $this->hasMany(DmeSubModule::class, 'dme_sub_unit_id');
    }
}
