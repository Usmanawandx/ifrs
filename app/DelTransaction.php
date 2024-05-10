<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DelTransaction extends Model
{
    protected $guarded = ['id'];
    protected $table = 'del_transactions';
}
