<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental_opportunitie extends Model
{
    use HasFactory;
    protected $table = 'rental_opportunitie';
    public $timestamps = false;
    protected $fillable = ['answer_id','form_id','store_id','sub_id','created_at','ip','customer_id','response_json','admin_response_email','admin_response_message','admin_response_status','referer_url','bookingdata'];

    function related()
    {
        return $this->belongsTo(Product::class);
    }
}
