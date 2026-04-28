<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['order_id', 'status', 'comment'])]
class OrderStatusHistory extends Model
{
    public $timestamps = false;
    protected $table = 'order_status_history';
    public function statusHistories()

    {
        return $this->hasMany(OrderStatusHistory::class)->orderByDesc('created_at');
    }

}