<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transporter extends Model
{
    protected $table = 'transporter';
    public $timestamps = false;

    public function vehicle()
    {
        return $this->hasMany(Vehicle::class,'id');

    }

}
