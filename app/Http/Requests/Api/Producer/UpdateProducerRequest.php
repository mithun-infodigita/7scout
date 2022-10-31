<?php

namespace App\Http\Requests\Api\Producer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProducerRequest extends FormRequest
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
                Rule::unique('producers')->ignore($this->id),
            ],
            'unique_id' => [
                'required',
                Rule::unique('producers')->ignore($this->id),
            ],
        ];
    }
}
