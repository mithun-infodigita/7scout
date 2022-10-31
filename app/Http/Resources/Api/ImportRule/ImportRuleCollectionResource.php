<?php

namespace App\Http\Resources\Api\ImportRule;

use App\Models\Column\Column;
use Illuminate\Http\Resources\Json\JsonResource;

class ImportRuleCollectionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                                =>  $this->id,
            'name'                              =>  $this->name,
            'import'                            =>  $this->import->name,
            'order_column'                      =>  $this->order_column,
            'source_reference_column_name'      =>  Column::find($this->source_reference_column_id)->name,
            'reference_compare_type'            =>  $this->reference_compare_type,
            'map_reference_script'              =>  $this->map_reference_script,
            'map_file_name'                     =>  $this->import->getMedia('additionalFiles')->pluck('name'),
            'map_column_name'                   =>  Column::find($this->map_column_id)->name,
            'map_value_script'                  =>  $this->map_value_script
        ];
    }
}
