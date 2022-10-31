<?php

namespace App\Http\Requests\Api\GroupColumn;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGroupColumnRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            //'name'  => 'required|unique:group_columns,name',
            'name' => [
                'required',
                Rule::unique('group_columns')->where(function ($query) {
                return $query->where('group_id', request()->group->id);
            })],
            'column_id'                 =>      'required',
        ];
    }
}
