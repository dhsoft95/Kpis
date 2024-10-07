<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpType extends Model
{
    use HasFactory;

    protected $connection = 'mysql_second';
    protected $fillable = ['type_name'];
    public $timestamps = false;
}
