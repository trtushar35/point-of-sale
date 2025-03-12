<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'products' => 'required|array',
                    'products.*.product_no' => 'required|string',
                    'products.*.name' => 'nullable|string',
                    'products.*.color' => 'required|integer',
                    'products.*.size' => 'required|integer',
                    'products.*.category' => 'required|integer',
                    'products.*.price' => 'required|numeric',
                    'products.*.quantity' => 'required|integer|min:1',
                    'total_price' => 'required|numeric',
                ];
                break;

            case 'PUT':
                return [
                    'products' => 'required|array',
                    'products.*.product_no' => 'required|string',
                    'products.*.name' => 'nullable|string',
                    'products.*.color' => 'required|integer',
                    'products.*.size' => 'required|integer',
                    'products.*.category' => 'required|integer',
                    'products.*.price' => 'required|numeric',
                    'products.*.quantity' => 'required|integer|min:1',
                    'total_price' => 'required|numeric',
                ];
                break;
            case 'PATCH':

                break;
        }
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, mixed>
     */
    public function messages()
    {

        return [
            'quantity.required' => 'The quantity is required',
            'price.required' => 'The price is required',
            'products.required' => 'The products list is required',
            'products.*.product_no.required' => 'The product number is required for each product',
            'products.*.name.required' => 'The product name is required for each product',
            'products.*.color.required' => 'The product color is required for each product',
            'products.*.size.required' => 'The product size is required for each product',
            'products.*.category.required' => 'The product category is required for each product',
            'products.*.price.required' => 'The product price is required for each product',
            'products.*.quantity.required' => 'The quantity for each product is required',
            'total_price.required' => 'The total price is required',
        ];
    }
}
