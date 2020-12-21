<?php

/**
 * Project: Payment-Point
 * File: Payment.php
 * Date: 12/16/20
 * Time: 19:15 PM
 * @author: muhammadikhsan
 * @copyright: IGN &copy; 2020
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'status',
        'status_code',
        'status_message',
        'payment_type',
        'product_id',
        'transaction_id',
        'currency',
        'transaction_status',
        'transaction_time'
    ];
}
