<?php

namespace App\Http\Requests\Api\CustomsSetting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomsSettingRequest extends FormRequest
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
            'customs_tariff_number_eu'  =>  'required|min:6',
            'customs_tariff_number_ch'  =>  'required|min:6',
            'import_fees_with_preferential_origin_of_goods_eu'  =>  'required|number',
            'import_fees_with_preferential_origin_of_goods_ch'  =>  'required|number',
            'tax_units_eu'  =>  'required',
            'tax_unist_ch'  =>  'required',
            'tax_by_values_eu'  =>  'required',
            'tax_by_values_ch'  =>  'required',
        ];
    }
}
