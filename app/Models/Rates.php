<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rates extends Model
{
    use HasFactory;
    protected $fillable = [
        'price',
        'transaction_type',
        'rate_definition_id',
        'start_at',
        'end_at',
        'product_id',
        'store_id',
        'crms_id',
    ];
    /**
     * Get the product that this rate belongs to.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
