<?php

namespace App\Http\Resources\Api\Ext\Group\GroupColumn;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupColumnTableCollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                    =>  $this->id,
            'column'                =>  $this->column->name,
            'name'                  =>  [
                'de'                    =>  $this->de_table,
                'en'                    =>  $this->en_table,
                'fr'                    =>  $this->fr_table,
                'it'                    =>  $this->it_table,
            ],

        ];
    }
}
