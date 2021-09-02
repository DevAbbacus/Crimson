<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class SubCategory extends BaseModel
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'product_subgroup';
    public $timestamps = true;
    protected $searcheable = [
        'name'
    ];



    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function scopeByUser($query, $id)
    {
        $query->where('user_id', $id);
    }

    public function scopeSearch($query, $search = '')
    {
            //echo "<pre>";print_r("test");exit();
        $query->when(!!$search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        });
    }

    public function subcategory()
    {
        return $this->belongsToMany('App\Models\SubCategory');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\SubCategory', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Models\SubCategory', 'parent_id');
    }
}
