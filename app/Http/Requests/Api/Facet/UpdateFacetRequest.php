<?php

namespace App\Http\Requests\Api\Facet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFacetRequest extends FormRequest
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
                Rule::unique('facets')->ignore($this->id),
            ],
            'column_id'  =>  'required'
        ];
    }
}
