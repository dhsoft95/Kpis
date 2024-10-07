<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $connection = 'mysql_second';
    protected $fillable = ['id', 'category_id', 'name'];
    public $timestamps = false;
    public $incrementing = false;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function utilityCodes()
    {
        return $this->belongsToMany(UtilityCode::class, 'subcategory_utility_codes');
    }
}
