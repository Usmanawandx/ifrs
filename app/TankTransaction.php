<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TankTransaction extends Model
{
    protected $table = 'tank_transaction';
    protected $guarded = ['id'];
}
