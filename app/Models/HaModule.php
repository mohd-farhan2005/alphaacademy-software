<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HaModule extends Model
{
    protected $fillable = ['ha_paper_id', 'name'];

    public function paper()
    {
        return $this->belongsTo(HaPaper::class, 'ha_paper_id');
    }

    public function units()
    {
        return $this->hasMany(HaUnit::class);
    }
}
