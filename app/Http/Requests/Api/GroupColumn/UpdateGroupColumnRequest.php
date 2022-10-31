<?php

namespace App\Http\Requests\Api\GroupColumn;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGroupColumnRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                Rule::unique('categories')->ignore($this->id),
            ]
        ];
    }
}
