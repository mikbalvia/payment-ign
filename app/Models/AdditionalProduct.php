<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdditionalProduct extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id','name', 'desc', 'image', 'price'
    ];

    public function product()
    {
        return $this->belongTo('App\Models\Product');
    }
}
