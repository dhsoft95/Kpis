<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    use HasFactory;


    protected $fillable = ['type_name', 'type_code'];
    public $timestamps = false;
}
