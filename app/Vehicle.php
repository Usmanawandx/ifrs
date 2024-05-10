<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicle';
    public $timestamps = false;

    public function transporter()
    {
        return $this->belongsTo(Transporter::class,'transporter_id');
    }
}
