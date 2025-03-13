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
                    'name' => 'nullable|string|max:255',
                    'color_id' => 'nullable|array', // Allow color_id to be an array
                    'color_id.*' => 'exists:colors,id', // Validate each color_id exists in the colors table
                    'size_id' => 'required|array', // Require size_id to be an array
                    'size_id.*' => 'exists:sizes,id', // Validate each size_id exists in the sizes table
                    'category_id' => 'required|exists:categories,id', // Ensure category_id exists in the categories table
                    'price' => 'required|numeric|min:0', // Ensure price is a number and is not negative
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image file
                ];
                break;

            case 'PUT':
                return [
                    'name' => 'nullable|string|max:255',
                    'color_id' => 'nullable|array', // Allow color_id to be an array
                    'color_id.*' => 'exists:colors,id', // Validate each color_id exists in the colors table
                    'size_id' => 'required|array', // Require size_id to be an array
                    'size_id.*' => 'exists:sizes,id', // Validate each size_id exists in the sizes table
                    'category_id' => 'required|exists:categories,id', // Ensure category_id exists in the categories table
                    'price' => 'required|numeric|min:0', // Ensure price is a number and is not negative
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image file
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
            'size_id.required' => __('The size field is required.'),
            'size_id.*.exists' => __('One or more selected sizes are invalid.'),
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
