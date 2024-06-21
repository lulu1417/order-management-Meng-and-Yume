<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::paginate());
    }

    public function store(StoreProductRequest $request)
    {
        Product::create($request->all());

        return response()->json(['success' => 'true']);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Product $product, UpdateProductRequest $request)
    {
        $product->update($request->all());

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        if (!$product->canDelete()){
            abort(Response::HTTP_CONFLICT, __('errors.product.can_not_delete'));
        }

        $product->delete();
        return response()->json(['success' => 'true']);
    }
}
