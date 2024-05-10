<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostDatedCheque extends Model
{
    protected $guarded = ['id'];

    public function child()
    {
        return $this->hasMany(\App\PostDatedChequeLine::class, 'post_dated_cheque_id');
    }
}
