<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class Package extends BaseModel
{
    use HasFactory;

    protected $guarded = [];
    protected $searcheable = [
        'name',
        'price',
        'discount',
        'description',
        'user_id'
    ];

    protected $relationships = ['products'];

    /**
     * Get the user that this product belongs to.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Get the image.
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }


    /**
     * Get all of the products based on the logged in user
     */
    public function scopeByUser($query, $id)
    {
        $query->where('user_id', $id);
    }

    /**
     * Filter the products based on user search
     */
    public function scopeSearch($query, $search = '')
    {
        $query->when(!!$search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('price', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('discount', 'like', '%' . $search . '%')
                    ->orWhere('package_type', 'like', '%' . $search . '%');
            });
        });
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product');
    }
}
