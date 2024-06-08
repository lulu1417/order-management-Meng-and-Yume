<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
      'no',
      'buyer_name'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    protected $appends = ['total_amount'];

    public function getTotalAmountAttribute()
    {
        return $this->items->sum(function ($orderItem) {
            return $orderItem->count * $orderItem->product->price;
        });
    }
}
