<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $connection = 'mysql_second';
    protected $fillable = ['id', 'name'];
    public $timestamps = false;
    public $incrementing = false;

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}
