<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerrapayConfig extends Model
{
    protected $connection = 'mysql_second';
    protected $table='terrapay_config';
    protected $fillable = ['enabled', 'allowed_corridors', 'allowed_currencies'];
    public $timestamps = false;

    protected $casts = [
        'enabled' => 'boolean',
        'allowed_corridors' => 'array',
        'allowed_currencies' => 'array',
    ];
}
