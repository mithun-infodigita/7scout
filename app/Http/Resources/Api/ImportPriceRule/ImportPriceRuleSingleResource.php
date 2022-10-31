<?php

namespace App\Http\Resources\Api\ImportPriceRule;

use App\Models\Category\Category;
use App\Models\Column\Column;
use App\Models\Group\Group;
use Illuminate\Http\Resources\Json\JsonResource;

class ImportPriceRuleSingleResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id'                                =>  $this->id,
            'name'                              =>  $this->name,
            'import'                            =>  $this->import->name,
            'order_column'                      =>  $this->order_column,
            'source_reference_column_id'        =>  $this->source_reference_column_id,
            'source_reference_column_name'      =>  Column::find($this->source_reference_column_id)->name,
            'reference_compare_type'            =>  $this->reference_compare_type,
            'map_reference_script'              =>  $this->map_reference_script,
            'map_file_id'                       =>  $this->map_file_id,
            'map_column_id'                     =>  $this->map_column_id,
            'map_value_script'                  =>  $this->map_value_script,
            'currency'                          =>  $this->currency,
            'country'                           =>  $this->country
        ];
    }


}
