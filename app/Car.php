<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = 'car';

    public function fuel()
    {
        return $this->belongsTo('App\Fuel');
    }
}
