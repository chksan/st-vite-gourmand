<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'menu_id', 'nb_personnes', 'total_price', 'delivery_address',
            'delivery_date', 'delivery_time', 'delivery_fee', 'status'])]
class Order extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function review(){
        return $this->hasOne(Review::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }


}