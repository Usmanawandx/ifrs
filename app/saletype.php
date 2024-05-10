<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class saletype extends Model
{
    protected $table = 'sale_type';
    public $timestamps = false;

    protected $fillable = ['name'];
}
