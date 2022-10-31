<?php

namespace App\Http\Requests\Api\Import;

use Illuminate\Foundation\Http\FormRequest;

class StoreImportPriceRuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                          =>  'required|min:3',
            'source_reference_column_id'    =>  'required',
            'map_reference_script'          =>  'required',
            'map_file_id'                   =>  'required',
            'country'                       =>  'required',
            'currency'                       =>  'required'
        ];
    }
}
