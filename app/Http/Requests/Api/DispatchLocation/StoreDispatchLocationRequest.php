<?php

namespace App\Http\Requests\Api\DispatchLocation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDispatchLocationRequest extends FormRequest
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
                Rule::unique('dispatch_locations')->ignore($this->id),
            ],
            'unique_id' => [
                'required',
                Rule::unique('dispatch_locations')->ignore($this->id),
            ],
        ];
    }
}
