<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlternativeProducts extends Model
{
    use HasFactory;

    protected $fillable = ['related_id', 'relatable_id'];

    function related()
    {
        return $this->belongsTo(Product::class);
    }
}
