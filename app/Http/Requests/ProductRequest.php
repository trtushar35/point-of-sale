<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'name' => 'nullable|string|max:255',
                    'color_id' => 'nullable|array',
                    'color_id.*' => 'exists:colors,id',
                    'sizes' => 'required|array',
                    'sizes.*.id' => 'required',
                    'sizes.*.quantity' => 'required',
                    'category_id' => 'required|exists:categories,id',
                    'price' => 'required|numeric|min:0',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ];
                break;
        }
    }

    public function messages()
    {

        return [
            'sizes.required' => __('The size field is required.'),
            'color_id.*.exists' => __('One or more selected colors are invalid.'),
            'category_id.required' => __('The category field is required.'),
            'category_id.exists' => __('The selected category is invalid.'),
            'price.required' => __('The price field is required.'),
            'price.numeric' => __('The price must be a number.'),
            'price.min' => __('The price must be at least 0.'),
            'image.image' => __('The file must be an image.'),
            'image.mimes' => __('The image must be a file of type: jpeg, png, jpg, gif, svg.'),
            'image.max' => __('The image may not be greater than 2MB.'),
        ];
    }
}
