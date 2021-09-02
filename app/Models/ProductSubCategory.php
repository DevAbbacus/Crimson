<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSubCategory extends Model
{
    use HasFactory;
    protected $table = 'subcategory_product';

    protected $fillable = ['relatable_id', 'sub_id'];

    function related()
    {
        return $this->belongsTo(Product::class);
    }
}
