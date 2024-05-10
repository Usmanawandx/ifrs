<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostDatedChequeLine extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function parent()
    {
        return $this->belongsTo(\App\PostDatedCheque::class, 'id');
    }
    public function account()
    {
        return $this->belongsTo(\App\Account::class, 'account_id');
    }

    public function bank()
    {
        return $this->belongsTo(\App\Banks::class, 'bank_id');
    }
}
