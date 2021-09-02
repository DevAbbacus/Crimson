<?php

namespace App\Models;

use App\Enums\AllowedStockType;
use App\Enums\StockMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Product extends BaseModel
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'buffer_percent',
        'replacement_charge',
        'notes',
        'weight',
        'barcode',
        'description',
        'purchase_price',
        'sub_rental_price',
        'active',
        'accessory_only',
        'discountable',
        'system',
        'tag_list',
        'custom_fields',
        'allowed_stock_type',
        'stock_method',
        'post_rent_unavailability',
        'product_group_id',
        'user_id',
        'crms_id',
        'icon',
        //
        'sale_revenue_group_id',
        'purchase_cost_group_id'
    ];

    protected $appends = ['stock_method_name', 'allowed_stock_type_name'];

    protected $casts = [
        'tag_list' => 'array',
        'custom_fields' => 'array'
    ];

    protected $searcheable = [
        'name',
        'description',
        'purchase_price',
        'weight',
    ];

    protected $relationships = [
        'productGroup',
        'alternativeProducts',
        'alternativeProducts.related',
        'rates',
        'image'
    ];

    #region Attributes
    public function getAllowedStockTypeNameAttribute()
    {
        $key = $this->allowed_stock_type;
        if (!$key) return null;
        return AllowedStockType::NAMES[$key];
    }

    public function getStockMethodNameAttribute()
    {
        $key = $this->stock_method;
        if (!$key) return null;
        return StockMethod::NAMES[$key];
    }
    #endregion

    /**
     * Get the product image.
     */
    public function image()
    {
        return $this->morphMany(File::class, 'target');
    }

    /**
     * Get the product group that this product belongs to.
     */
    public function productGroup()
    {
        return $this->belongsTo(ProductGroup::class);
    }

    /**
     * Get the user that this product belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the rate for the product.
     */
    public function rates()
    {
        return $this->hasMany(Rates::class);
    }

    /**
     * Get the accessories for the product.
     */
    public function accessories()
    {
        return $this->hasMany(Accessories::class);
    }

    /**
     * Get The Product Accessories for the Product.
     */
    public function productAccessories() {
        return $this->hasMany(ProductAccessorie::class);
    }

    /**
     * Get the alternative products for the product.
     */
    public function alternativeProducts()
    {
        return $this->hasMany(AlternativeProducts::class, 'relatable_id');
    }

    public function productsubcategory()
    {
        return $this->hasMany(ProductSubCategory::class, 'relatable_id');
    }

    
    public function customFields() {
        return DB::table('custom_fields')->select('product_height_mm', 'product_width_mm','weight_kg','specname_1', 'specvalue_1','specname_2', 'specvalue_2','specname_3', 'specvalue_3','specname_4', 'specvalue_4','specname_5', 'specvalue_5','specname_6', 'specvalue_6','colour_temperature', 'power_type', 'output_at_8m', 'output_at_5m', 'output_at_2m', 'power_input_watts','optional_accessory_1', 'optional_accessory_2', 'optional_accessory_3', 'optional_accessory_4', 'discount_start', 'discount_end', 'discount_percentage','discount_amount','lighthouse_category','alternate_search_terms', 'when_booked_separately', 'lighthouse_sort_order','usability','gaffer_tips','gaffer_notes', 'prep_tasks', 'post_tasks')->where('fieldable_id', $this->id)->where('fieldable_type', 'Product')->get();
    }

    public function customFieldsMorph()
    {
        return $this->hasOne(CustomFields::class, 'fieldable_id');//->where('fieldable_type', 'Product');
    }

    /**
     * Get all of the products based on the logged in user
     */
    public function scopeByUser($query, $id)
    {
        $query->where('user_id', $id);
    }

    /**
     * Get all of the products based on the logged in user
     */
    public function scopeInGroups($query, $groups = [])
    {
        $query->when(count($groups) > 0, function ($q) use ($groups) {
            $q->whereIn('product_group_id', $groups);
        });
    }

    public static function list($params = [], $user_id = null,$customFields = null)
    {
        $query = parent::list($params);

        if(!empty($customFields)){

            $query->whereHas('customFieldsMorph',  function($q) use ($customFields){
                return $q->blanks($customFields);
            });
        }

        $groups = isset($params['groups']) ? $params['groups'] : [];
        $query = $query->byUser($user_id)->inGroups($groups);
        return $query;
    }

}
