<?php

namespace App\Models;

use App\Enums\InclusionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accessories extends Model
{
    use HasFactory;

    protected $fillable = [
        'relatable_id',
        'related_id',
        'inclusion_type',
        'parent_transaction_type',
        'item_transaction_type',
        'mode',
        'sort_order',
        'quantity',
        'zero_priced',
        'crms_id',
    ];


    #region Attributes
    public function getInclusionTypeNameAttribute()
    {
        $number = $this->inclusion_type;
        return InclusionType::NAMES[$number];
    }
    #endregion

    #region Relations
    public function related()
    {
        return $this->belongsTo(Product::class);
    }
    #endregion
}
