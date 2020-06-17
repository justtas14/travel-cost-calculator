<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fuel extends Model
{
    protected $table = 'fuels';

    public function cars()
    {
        return $this->hasMany('App\Car');
    }
}
