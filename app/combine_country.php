<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class combine_country extends Model
{
    //
    protected $table = 'combine_country';
    public $timestamps = false;


    public function countrys()
    {
        return $this->belongsTo(\App\country::class,'country');
    }
   
    public function citys()
    {
        return $this->belongsTo(\App\country::class,'city');
    }
   

    public function states()
    {
        return $this->belongsTo(\App\country::class,'state');
    }
   


   



}
