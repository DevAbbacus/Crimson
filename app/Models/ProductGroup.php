<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'crms_id',
        'user_id'
    ];

    /**
     * Get the user that this product group belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product category icon.
     */
    public function image()
    {
        return $this->morphOne(File::class, 'target');
    }

    /**
     * Get all of the product groups based on the logged in user
     */
    public function scopeByUser($query, $id)
    {
        $query->select('id', 'name')->where('user_id', $id);
    }
}
