<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banks extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    public function post_date_chequeline()
    {
        return $this->hasOne(\App\PostDatedChequeLine::class, 'id');
    }
}
