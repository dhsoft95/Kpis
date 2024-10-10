<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityCode extends Model
{
    use HasFactory;

//    protected $connection = 'mysql_second';
    protected $fillable = ['code', 'description'];
    public $timestamps = false;

    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class, 'subcategory_utility_codes');
    }
}
