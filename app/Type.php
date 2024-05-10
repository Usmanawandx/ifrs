<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table = 'type';
    public $timestamps = false;
    
    
    protected $fillable = ['name','type','prefix','purchase_type','is_milling'];
}
