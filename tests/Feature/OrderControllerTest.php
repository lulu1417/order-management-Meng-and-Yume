<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $orders = Order::factory()->count(2)->create();
        $product = Product::factory()->create(
            [
                'price' => 50
            ]
        );

        foreach ($orders as $index => $order) {
            $order->items()->create(
                [
                    'product_id' => $product->id,
                    'count' => $index + 1,
                ]
            );
        }

        $res = $this->get('api/orders');
        $res->assertJson(
            [
                [
                    'id' => $orders[0]->id,
                    'no' => $orders[0]->no,
                    'buyer_name' => $orders[0]->buyer_name,
                    'total_amount' => 50,
                    'created_at' => Carbon::parse(
                        $orders[0]->created_at
                    )->toISOString(),
                ],
                [
                    'id' => $orders[1]->id,
                    'no' => $orders[1]->no,
                    'buyer_name' => $orders[1]->buyer_name,
                    'total_amount' => 100,
                    'created_at' => Carbon::parse(
                        $orders[1]->created_at
                    )->toISOString(),
                ],
            ]
        );
    }

    public function testStore()
    {
        $product = Product::factory()->create();

        $this->withHeader('Accept', 'application/json')
            ->post(
                'api/orders',
                [
                    'buyer_name' => $buyerName = 'buyer_1',
                    'items' =>
                        [
                            [
                                'product_id' => $product->id,
                                'count' => 3
                            ]
                        ]
                ]
            )->assertSuccessful();

        $this->assertDatabaseHas(
            'orders',
            [
                'buyer_name' => $buyerName
            ]
        );

        $order = Order::first();

        $this->assertDatabaseHas(
            'order_items',
            [
                'order_id' => $order->id,
                'product_id' => $product->id,
                'count' => 3
            ]
        );
    }

    public function testShow()
    {
        $order = Order::factory()->create();
        $product = Product::factory()->create(
            [
                'price' => 50
            ]
        );

        $order->items()->create(
            [
                'product_id' => $product->id,
                'count' => 1,
            ]
        );

        $res = $this->get("api/orders/$order->id");
        $res->assertJson(
            [
                'id' => $order->id,
                'no' => $order->no,
                'buyer_name' => $order->buyer_name,
                'total_amount' => 50,
                'created_at' => Carbon::parse(
                    $order->created_at
                )->toISOString(),
                'items' => [
                    [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'count' => 1,
                        'product' =>
                            [
                                'name' => $product->name,
                                'price' => $product->price
                            ]
                    ],
                ],
            ]
        );
    }

    public function testUpdate()
    {
        $order = Order::factory()->has(
            OrderItem::factory()->count(2),
            'items'
        )->create();

        $items = $order->items()->get();

        $this->withHeader('Accept', 'application/json')
            ->put(
                "api/orders/$order->id",
                [
                    'buyer_name' => 'updatedBuyerName',
                    'items' =>
                        [
                            [
                                'id' => $items[0]->id,
                                'product_id' => $updatedProductId
                                    = Product::factory()
                                    ->create()->id,
                                'count' => 1
                            ],
                            [
                                'id' => $items[1]->id,
                                '_delete' => true
                            ],
                            [
                                'product_id' => $newProductId
                                    = Product::factory()
                                    ->create()->id,
                                'count' => 3
                            ]
                        ]
                ]
            )->assertSuccessful();

        $this->assertDatabaseHas(
            'orders',
            [
                'buyer_name' => 'updatedBuyerName'
            ]
        );

        $this->assertDatabaseHas(
            'order_items',
            [
                'id' => 1,
                'product_id' => $updatedProductId,
                'order_id' => $order->id,
                'count' => 1
            ]
        );

        $this->assertDatabaseHas(
            'order_items',
            [
                'id' => 3,
                'product_id' => $newProductId,
                'order_id' => $order->id,
                'count' => 3
            ]
        );

        $this->assertDatabaseMissing(
            'order_items',
            [
                'id' => 2,
                'order_id' => $order->id,
            ]
        );
    }

    public function testDestroy()
    {
        $order = Order::factory()
            ->has(OrderItem::factory()->count(3), 'items')
            ->create();

        $this->withHeader('Accept', 'application/json')
            ->delete("api/orders/$order->id")
            ->assertSuccessful();

        $this->assertDatabaseMissing(
            'orders',
            [
                'id' => $order->id
            ]
        );
        $this->assertDatabaseMissing(
            'order_items',
            [
                'order_id' => $order->id
            ]
        );
    }
}
