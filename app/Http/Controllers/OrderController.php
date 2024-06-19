<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items.product'])->get();
        return response()->json($orders);
    }

    public function store(StoreOrderRequest $request)
    {
        return $this->handleTransaction(
            'OrderController@store error',
            function () use ($request) {
                $order = Order::create(
                    [
                        'no' => Str::uuid()->toString(),
                        'buyer_name' => $request->buyer_name
                    ]
                );
                foreach ($request->items as $item) {
                    $order->items()->create($item);
                }
                return response()->json(['success' => 'true']);
            }
        );
    }

    public function show(Order $order)
    {
        return response()->json($order->load('items.product'));
    }

    public function update(Order $order, UpdateOrderRequest $request)
    {
        return $this->handleTransaction(
            'OrderController@update error',
            function () use ($order, $request) {
                $order->update($request->only('buyer_name'));
                if ($request->has('items')) {
                    foreach ($request->items as $item) {
                        if (isset($item['id'])) {
                            if (isset($item['_delete']) && $item['_delete']) {
                                OrderItem::destroy($item['id']);
                            } else {
                                $orderItem = OrderItem::findOrFail($item['id']);
                                $orderItem->update($item);
                            }
                        } else {
                            $order->items()->create($item);
                        }
                    }
                }
                return response()->json($order->load('items.product'));
            }
        );
    }

    public function destroy(Order $order)
    {
        $this->handleTransaction(
            'OrderController@destroy error',
            function () use ($order) {
                $order->items()->delete();
                $order->delete();
            }
        );

        return response()->json(['success' => 'true']);
    }
}
