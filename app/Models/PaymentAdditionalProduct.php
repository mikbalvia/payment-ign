<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAdditionalProduct extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id', 'additional_product_id', 'quantity'
    ];
}
