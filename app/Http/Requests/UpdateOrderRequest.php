<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'buyer_name' =>
                [
                    'sometimes',
                    'required',
                    'string',
                    'max:255'
                ],
            'items' => [
                'sometimes',
                'required',
                'array',
            ],
            'items.*.id' => [
                'exists:order_items,id'
            ],
            'items.*.product_id' =>
                [
                    'exists:products,id'
                ],
            'items.*.count' =>
                [
                    'required_with:items.*.product_id',
                    'integer',
                    'min:1'
                ],
            'order_items.*._delete' =>
                [
                    'sometimes',
                    'boolean'
                ]
        ];
    }
}
