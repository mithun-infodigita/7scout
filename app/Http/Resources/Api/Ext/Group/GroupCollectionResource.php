<?php

namespace App\Http\Resources\Api\Ext\Group;

use App\Http\Resources\Api\Ext\Category\CategorySingleResource;
use App\Http\Resources\Api\Ext\Category\CategorySingleResourceForGroups;
use App\Http\Resources\Api\Ext\Group\GroupColumn\GroupColumnCollectionResource;
use App\Http\Resources\Api\Ext\Group\GroupColumn\GroupColumnDetailCollectionResource;
use App\Http\Resources\Api\Ext\Group\GroupColumn\GroupColumnTableCollectionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupCollectionResource extends JsonResource
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
            'name'                  =>  [
                'de'                    =>  $this->de,
                'en'                    =>  $this->en,
                'fr'                    =>  $this->fr,
                'it'                    =>  $this->it,
            ],

            'columns'                                   =>  GroupColumnCollectionResource::collection($this->groupColumns->where('show_in_table')->sortBy('order_column')),
            'show_in_table'                             =>  GroupColumnTableCollectionResource::collection($this->groupColumns->where('show_in_table')->sortBy('order_column')),
            'show_in_table_detail'                      =>  GroupColumnDetailCollectionResource::collection($this->groupColumns->where('show_in_table_detail')->sortBy('order_column')),
            'show_in_detail_page'                       =>  GroupColumnDetailCollectionResource::collection($this->groupColumns->where('show_in_detail_page')->sortBy('order_column')),
            'left_side_filter'                          =>  GroupColumnTableCollectionResource::collection($this->groupColumns->where('left_side_filter')->sortBy('order_column')),
            'detail_filter'                             =>  GroupColumnTableCollectionResource::collection($this->groupColumns->where('detail_filter')->sortBy('order_column')),
            'category_id'                               =>  $this->category_id,
            'category'                                  =>  new CategorySingleResourceForGroups($this->category),

            'show_in_page_detail'                       =>  GroupColumnDetailCollectionResource::collection($this->groupColumns->where('show_in_detail_page')->sortBy('order_column')),
        ];
    }
}
