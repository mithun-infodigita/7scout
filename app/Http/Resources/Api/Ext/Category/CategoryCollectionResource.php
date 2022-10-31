<?php

namespace App\Http\Resources\Api\Ext\Category;

use App\Http\Resources\Api\Ext\Facet\FacetCollectionResource;
use App\Http\Resources\Api\Ext\Group\GroupSingleResourceForCategories;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryCollectionResource extends JsonResource
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
            'facets'                =>  FacetCollectionResource::collection($this->facets),
            'group_id'              =>  $this->group->id,
            'group'                 =>  new GroupSingleResourceForCategories($this->group),
            'image_url'             =>  $this->getFirstMediaUrl('categoryImage'),
            'drawing_url'           =>  $this->getFirstMediaUrl('categoryDrawing')
        ];
    }
}
