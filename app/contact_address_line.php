<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class contact_address_line extends Model
{
    //
    public function address(){
        return $this->belongsTo(Contact::class,'contact_id','id');
    }
}
