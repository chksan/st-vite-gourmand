<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class OrderStat extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'order_stats';

    protected $fillable = [
        'order_id',
        'menu_id',
        'menu_title',
        'nb_personnes',
        'menu_price',
        'delivery_fee',
        'total_price',    //full price (with delivery fees included)
        'ordered_at',
    ];

    protected $casts = [
        'ordered_at' => 'datetime',
    ];
}