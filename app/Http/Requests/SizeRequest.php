<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SizeRequest extends FormRequest
{

    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'category_id' => 'required',
                    'sizes' => 'required|array|min:1',
                ];
                break;

            case 'PUT':
                return [
                    'category_id' => 'required',
                    'sizes.*' => 'required|string|max:255',
                ];
                break;
            case 'PATCH':

                break;
        }
    }

    public function messages()
    {

        return [
            'category_id.required' => 'The category field is required',
            'size.required' => 'The size field is required',
        ];
    }
}
