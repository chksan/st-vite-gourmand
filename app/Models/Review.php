<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['order_id', 'user_id', 'rating', 'comment', 'is_validated', 'validated_by'])]
class Review extends Model
{
    use HasFactory;

    public function order()
    {
        return $this->belongsTo(\App\Models\Order::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}