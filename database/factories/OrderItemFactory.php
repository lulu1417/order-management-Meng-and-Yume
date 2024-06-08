<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'order_id' => function (){
                return Order::factory()->create()->id;
            },
            'product_id' => function (){
                return Product::factory()->create()->id;
            },
            'count' => $this->faker->randomDigit(),
        ];

    }
}
