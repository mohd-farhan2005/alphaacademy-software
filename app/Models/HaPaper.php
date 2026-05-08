<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HaPaper extends Model
{
    protected $fillable = ['name'];

    public function modules()
    {
        return $this->hasMany(HaModule::class);
    }
}
