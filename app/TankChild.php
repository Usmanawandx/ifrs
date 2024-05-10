<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TankChild extends Model
{
    protected $table        = 'tank_child';
    protected $guarded      = ['id'];
    public    $timestamps   = false;

}
