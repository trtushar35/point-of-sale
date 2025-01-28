<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
                    'name' => 'required',
                    'color_id' => 'nullable',
                    'size_id' => 'required',
                    'category_id' => 'required',
                    'price' => 'required',
                    'image' => 'nullable',
                ];
                break;

            case 'PUT':
                return [
                    'name' => 'required',
                    'color_id' => 'nullable',
                    'size_id' => 'required',
                    'category_id' => 'required',
                    'price' => 'required',
                    'image' => 'nullable',
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
            'name.required' => __('The name field is required.'),
            'size_id.required' => __('The size field is required.'),
            'category_id.required' => __('The category field is required.'),
            'price.required' => __('The price field is required.'),
        ];
    }
}
