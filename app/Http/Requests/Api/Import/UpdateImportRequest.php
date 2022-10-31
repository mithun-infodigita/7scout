<?php

namespace App\Http\Requests\Api\Import;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImportRequest extends FormRequest
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
            'name'                              =>  'required|min:3',
            'producer_id'                       =>  'required',
            'language'                          =>  'requiredIf:type,map from record single language',
            'group_mapping.*.map_script'        =>  'required',
            'group_mapping.*.validation_string' =>  'required',
            'group_mapping.*.group_id'          =>  'required',
            'category_mapping.*.map_script'     =>  'required',
            'category_mapping.*.validation_string'      =>  'required',
            'category_mapping.*.category_id'            =>  'required',
        ];
    }

    public function messages()
    {
        return [
            'group_mapping.*.map_script.required'                       =>  __('validation.custom.map_script.required'),
            'group_mapping.*.validation_string.required'                =>  __('validation.custom.validation_script.required'),
            'group_mapping.*.group_id.required'                         =>  __('validation.custom.group_id.required'),
            'category_mapping.*.map_script.required'                    =>  __('validation.custom.map_script.required'),
            'category_mapping.*.validation_string.required'             =>  __('validation.custom.validation_script.required'),
            'category_mapping.*.category_id.required'                   =>  __('validation.custom.category_id.required')
        ];

    }
}
