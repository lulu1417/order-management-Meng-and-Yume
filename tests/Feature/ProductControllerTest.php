<?php

namespace Tests\Feature;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $products = Product::factory()->count(2)->create();

        $res = $this->get('api/products');
        $res->assertJson(
            [
                'data' => [
                    [
                        'id' => $products[0]->id,
                        'name' => $products[0]->name,
                        'price' => $products[0]->price
                    ],
                    [
                        'id' => $products[1]->id,
                        'name' => $products[1]->name,
                        'price' => $products[1]->price
                    ],
                ]
            ]
        );
    }

    public function testStore()
    {
        $this->withHeader('Accept', 'application/json')
            ->post(
                'api/products',
                [
                    'name' => $name = 'firstProduct',
                    'price' => 50.33
                ]
            )->assertSuccessful();

        $this->assertDatabaseHas(
            'products',
            [
                'name' => $name,
                'price' => 50.33
            ]
        );
    }

    public function testShow()
    {
        $product = Product::factory()->create(
            [
                'price' => 50
            ]
        );

        $res = $this->get("api/products/$product->id");
        $res->assertJson(
            [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price
            ]
        );
    }

    public function testUpdate()
    {
        $product = Product::factory()->create();

        $this->withHeader('Accept', 'application/json')
            ->put(
                "api/products/$product->id",
                [
                    'name' => 'updatedProductName',
                    'price' => 100
                ]
            )->assertSuccessful();

        $this->assertDatabaseHas(
            'products',
            [
                'name' => 'updatedProductName',
                'price' => 100
            ]
        );
    }

    public function testDestroy()
    {
        $product = Product::factory()->create();

        $this->withHeader('Accept', 'application/json')
            ->delete("api/products/$product->id")
            ->assertSuccessful();

        $this->assertDatabaseMissing(
            'products',
            [
                'id' => $product->id
            ]
        );
    }

    public function testDestroyWithExistedItems()
    {
        $product = Product::factory()->has(
            OrderItem::factory()->count(2),
            'items'
        )->create();

        $this->withHeader('Accept', 'application/json')
            ->delete("api/products/$product->id")
            ->assertStatus(Response::HTTP_CONFLICT);

        $this->assertDatabaseHas(
            'products',
            [
                'id' => $product->id
            ]
        );
    }
}
