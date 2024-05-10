<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class category_list extends Model
{
    protected $table = 'category_list';
    public $timestamps = false;
    
    protected $fillable = ['name'];

    public function subcategory()
    {
        return $this->hasMany(\App\subcategory::class,'parent_id','id');
    }
}
