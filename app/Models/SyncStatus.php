<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncStatus extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'status',
        'user_id',
        'last_sync',
    ];

    function scopeByUser($query, $id)
    {
        $query->where('user_id', $id);
    }

    function scopeIsWorking($query)
    {
        $query->where('status', 0);
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
