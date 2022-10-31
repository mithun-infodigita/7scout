<?php

namespace App\Http\Requests\Api\CustomsSetting;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomsSettingRequest extends FormRequest
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
            'import_fees_with_preferential_origin_of_goods_eu'  =>  'required|numeric',
            'import_fees_with_preferential_origin_of_goods_ch'  =>  'required|numeric',
            'import_fees_without_preferential_origin_of_goods_eu'  =>  'required|numeric',
            'import_fees_without_preferential_origin_of_goods_ch'  =>  'required|numeric',
            'tax_unit_eu'  =>  'required',
            'tax_unit_ch'  =>  'required',
            'tax_by_value_eu'  =>  'required',
            'tax_by_value_ch'  =>  'required',
        ];
    }
}
