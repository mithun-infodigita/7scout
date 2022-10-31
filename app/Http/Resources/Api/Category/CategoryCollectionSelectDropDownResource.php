<?php

namespace App\Http\Resources\Api\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryCollectionSelectDropDownResource extends JsonResource
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
            'label'                 =>  $this->id." - ".$this->name,
            'children'              =>  CategoryCollectionSelectDropDownResource::collection($this->categories)
        ];
    }

}
