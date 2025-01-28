<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryRequest extends FormRequest
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
                    'category_id' => 'required',
                    'quantity' => 'required',
                    'stock_in' => 'nullable',
                    'stock_out' => 'nullable',
                    'sku' => 'nullable',
                ];
                break;

            case 'PUT':
                return [
                    'category_id' => 'required',
                    'quantity' => 'required',
                    'stock_in' => 'nullable',
                    'stock_out' => 'nullable',
                    'sku' => 'nullable',
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
            'category_id.required' => 'The category field is required',
            'quantity.required' => 'The quantity field is required',
        ];
    }
}
