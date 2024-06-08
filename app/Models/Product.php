<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable
        = [
            'name',
            'price'
        ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function canDelete()
    {
        if ($this->items->count() == 0) {
            return true;
        }

        return false;
    }
}
