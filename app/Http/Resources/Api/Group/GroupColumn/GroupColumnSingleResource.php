<?php

namespace App\Http\Resources\Api\Group\GroupColumn;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupColumnSingleResource extends JsonResource
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
            'id'                                =>  $this->id,
            'name'                              =>  $this->name,
            'column_id'                         =>  $this->column_id,
            'de_table'                          =>  $this->de_table,
            'en_table'                          =>  $this->en_table,
            'fr_table'                          =>  $this->fr_table,
            'it_table'                          =>  $this->it_table,
            'de_detail'                         =>  $this->de_detail,
            'en_detail'                         =>  $this->en_detail,
            'fr_detail'                         =>  $this->fr_detail,
            'it_detail'                         =>  $this->it_detail,
            'show_in_table'                     =>  $this->show_in_table,
            'show_in_table_detail'              =>  $this->show_in_table_detail,
            'show_in_detail_page'               =>  $this->show_in_detail_page,
            'detail_filter'                     =>  $this->detail_filter,
            'left_side_filter'                  =>  $this->left_side_filter,
        ];
    }
}
