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
            'order_items' => [
                'sometimes',
                'required',
                'array',
            ],
            'order_items.*.id' => [
                'exists:order_items,id'
            ],
            'order_items.*.product_id' =>
                [
                    'exists:products,id'
                ],
            'order_items.*.count' =>
                [
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
