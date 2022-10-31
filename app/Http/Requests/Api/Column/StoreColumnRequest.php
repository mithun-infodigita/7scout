<?php

namespace App\Http\Requests\Api\Column;

use Illuminate\Foundation\Http\FormRequest;

class StoreColumnRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'  => 'required|unique:columns,name',
            'type'  =>  'required'
        ];
    }
}
