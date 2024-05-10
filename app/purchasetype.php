<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class purchasetype extends Model
{
    protected $table = 'purchase_type';
    public $timestamps = false;
    protected $fillable = ['Type','prefix', 'control_account_id'];
}
