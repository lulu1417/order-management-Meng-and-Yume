<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['items.product'])->get();
        return response()->json($orders->load('items.product'));
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            DB::beginTransaction();
            $order = Order::create(
                [
                    'no' => hash_hmac('sha256', time(), 'orderNo'),
                    'buyer_name' => $request->buyer_name
                ]
            );
            foreach ($request->items as $item) {
                $order->items()->create(
                    [
                        'product_id' => $item['product_id'],
                        'count' => $item['count']
                    ]
                );
            }

            DB::commit();

            return response()->json(['success' => 'true']);
        } catch (\Throwable $throwable) {
            DB::rollBack();

            @Log::error(
                'OrderController@update error',
                ['message' => $throwable->getMessage()]
            );
            throw $throwable;
        }
    }

    public function show(Order $order)
    {
        return response()->json($order->load('items.product'));
    }

    public function update(Order $order, UpdateOrderRequest $request)
    {
        try {
            DB::beginTransaction();

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

            DB::commit();

            return response()->json($order->load('items.product'));
        } catch (\Throwable $throwable) {
            DB::rollBack();

            @Log::error(
                'OrderController@update error',
                [
                    'message' => $throwable->getMessage()
                ]
            );
            throw $throwable;
        }
    }

    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();

            $order->items()->delete();
            $order->delete();

            DB::commit();
            return response()->json(['success' => 'true']);
        } catch (\Throwable $throwable) {
            DB::rollBack();

            @Log::error(
                'OrderController@destroy error',
                [
                    'message' => $throwable->getMessage()
                ]
            );
            throw $throwable;
        }
    }
}
