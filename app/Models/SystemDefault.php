<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemDefault extends Model
{

    protected $fillable = ['key_name', 'value'];
    public $timestamps = false;
}
