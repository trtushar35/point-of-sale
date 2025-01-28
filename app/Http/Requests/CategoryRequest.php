<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{

    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'name' => 'required',
                ];
                break;

            case 'PUT':
                return [
                    'name' => 'required',
                ];
                break;
            case 'PATCH':

                break;
        }
    }

    public function messages()
    {

        return [
            'name.required' => 'The name field is required',
        ];
    }
}
