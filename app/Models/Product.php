<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'desc', 'image', 'price', 'endpoint', 'code', 'tnc_url'
    ];

    public function additionalProduct()
    {
        return $this->hasMany('App\Models\AdditionalProduct');
    }
}
