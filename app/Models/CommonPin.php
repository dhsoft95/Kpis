<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonPin extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second';
    protected $fillable = ['pin'];
    public $timestamps = false;
}