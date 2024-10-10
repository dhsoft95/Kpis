<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemboPlusConfig extends Model
{

    protected $table='tembo_plus_config';
    protected $fillable = ['callback_url', 'forwarding_secret'];
    public $timestamps = false;
}
