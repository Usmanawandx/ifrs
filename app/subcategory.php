<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class subcategory extends Model
{
    protected $table = 'subcategory';
    public $timestamps = false;
    
    protected $fillable = ['parent_id','name'];

    public function category()
    {
        return $this->belongsTo(category_list::class,'parent_id','id');
    }

    public function subcategory()
    {
        return $this->hasMany(\App\child_cat::class,'sub','id');
    }



}
