<?php

namespace App\Http\Requests;

use App\Enums\AllowedStockType;
use App\Enums\StockMethod;
use App\Http\Requests\Request;

class ProductRequest extends Request
{
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
            ],
            'description' => [
                'nullable',
                'string',
                'max:30000',
            ],
            'allowed_stock_type' => [
                'required',
                'in:' . AllowedStockType::all()->map->id()->implode(','),
            ],
            'stock_method' => [
                'required',
                'in:' . StockMethod::all()->map->id()->implode(','),
            ],
            'replacement_charge' => [
                'nullable',
                'string',
            ],
            'weight' => [
                'nullable',
                'string',
            ],
            'product_group_id' => [
                'required',
                'integer',
            ],
            'purchase_price' => [
                'nullable',
                'string',
            ],
            'sub_rental_price' => [
                'nullable',
                'string',
            ],
            'rental_revenue_group' => [
                'nullable',
                'integer',
            ],
            'sale_revenue_group' => [
                'nullable',
                'integer',
            ],
            'sub_rental_cost_group' => [
                'nullable',
                'integer',
            ],
            'sub_rental_rate_definition' => [
                'nullable',
                'integer',
            ],
            'purchase_cost_group' => [
                'nullable',
                'integer'
            ]
        ];
    }
}
