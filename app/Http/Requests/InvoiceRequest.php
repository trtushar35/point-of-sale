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
                    'product_no' => 'nullable',
                    'quantity' => 'required',
                    'price' => 'required',
                    'discount' => 'nullable',
                    'total_price' => 'required',
                ];
                break;

            case 'PUT':
                return [
                    'product_no' => 'nullable',
                    'quantity' => 'required',
                    'price' => 'required',
                    'discount' => 'nullable',
                    'total_price' => 'required',
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
        ];
    }
}
