<?php

namespace App\Http\Requests\Api\Facet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFacetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'      => 'required|unique:facets,name',
            'column_id' =>  'required'
        ];
    }
}
