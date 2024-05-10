<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class child_cat extends Model
{
    protected $table = 'child_category';
    public $timestamps = false;
    
      
     protected $fillable = ['name','sub'];
     
       public function child()
    {
        return $this->belongsTo(subcategory::class,'sub','id');
    }
    
    
}
