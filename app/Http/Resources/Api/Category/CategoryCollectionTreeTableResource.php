<?php

namespace App\Http\Resources\Api\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryCollectionTreeTableResource extends JsonResource
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
            'parent_id'             =>  $this->parent_id,
            'name'                  =>  $this->name,
            'level'                 =>  $this->level,
            'de'                    =>  $this->de,
            'en'                    =>  $this->en,
            'fr'                    =>  $this->fr,
            'it'                    =>  $this->it,
            'data'                  =>  CategoryCollectionTreeTableResource::collection($this->categories),
        ];
    }
}
