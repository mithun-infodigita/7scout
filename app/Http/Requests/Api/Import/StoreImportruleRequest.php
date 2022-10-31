<?php

namespace App\Http\Requests\Api\Import;

use Illuminate\Foundation\Http\FormRequest;

class StoreImportruleRequest extends FormRequest
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
            'name'              =>  'required|min:3',
            'column'            =>  'required',
            'rule_type'         =>  'required',
            'group_id'          =>  'requiredIf:rule_type,group',
            'category_id'       =>  'requiredIf:rule_type,category',
            'text_value'        =>  'requiredIf:rule_type,fix_text',
            'map_value_script'  =>  'requiredIf:rule_type,map',
            'part_value_script' =>  'requiredIf:rule_type,category|requiredIf:rule_type,group',
            'compare_type'      =>  'requiredIf:rule_type,category|requiredIf:rule_type,group',
            'compare_value'     =>  'requiredIf:rule_type,category|requiredIf:rule_type,group',
        ];
    }
}
