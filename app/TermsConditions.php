<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TermsConditions extends Model
{
    protected $table = 'Terms_Conditions';
    public $timestamps = false;
    
    protected $fillable = ['name'];
}