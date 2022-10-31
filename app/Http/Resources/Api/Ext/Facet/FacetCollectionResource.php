<?php

namespace App\Http\Resources\Api\Ext\Facet;

use Illuminate\Http\Resources\Json\JsonResource;

class FacetCollectionResource extends JsonResource
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
            'name'                  =>  $this->name,
            'de'                    =>  $this->de,
            'en'                    =>  $this->en,
            'fr'                    =>  $this->fr,
            'it'                    =>  $this->it,
            'column_id'             =>  $this->column_id,
            'column_name'           =>  $this->column->name,
            'item_sort'             =>  $this->item_sort,
            'sort'                  =>  $this->order_column
        ];
    }
}

