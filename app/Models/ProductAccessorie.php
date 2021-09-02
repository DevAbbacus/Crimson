<?php

namespace App\Models;

use App\Enums\InclusionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAccessorie extends Model
{
    //protected $visible = ['crms_id'];
    protected $fillable = [
        'product_id',
        'relatable_id',
        'relatable_type',
        'related_id',
        'related_type',
        'related_name',
        'related_icon_url',
        'related_icon_thumb_url',
        'type',
        'parent_transaction_type',
        'parent_transaction_type_name',
        'item_transaction_type',
        'item_transaction_type_name',
        'inclusion_type',
        'inclusion_type_name',
        'mode',
        'mode_name',
        'quantity',
        'zero_priced',
        'sort_order',
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
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    #endregion
}
