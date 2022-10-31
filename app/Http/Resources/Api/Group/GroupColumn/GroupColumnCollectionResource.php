<?php

namespace App\Http\Resources\Api\Group\GroupColumn;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupColumnCollectionResource extends JsonResource
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
            'column_id'             =>  $this->column_id,
            'column_name'           =>  $this->column->name,
            'de_table'              =>  $this->de_table,
            'en_table'              =>  $this->en_table,
            'fr_table'              =>  $this->fr_table,
            'it_table'              =>  $this->it_table,
            'de_detail'              =>  $this->de_detail,
            'en_detail'              =>  $this->en_detail,
            'fr_detail'              =>  $this->fr_detail,
            'it_detail'              =>  $this->it_detail,
            'show_in_table_label'             =>  $this->show_in_table ? "<span class='active_badge'>$this->show_in_table</span>" : "<span class='inactive_badge'>0</span>",
            'show_in_table_detail_label'      =>  $this->show_in_table_detail ? "<span class='active_badge'>$this->show_in_table_detail</span>" : "<span class='inactive_badge'>0</span>",
            'show_in_detail_page_label'      =>  $this->show_in_detail_page ? "<span class='active_badge'>$this->show_in_detail_page</span>" : "<span class='inactive_badge'>0</span>",
            'left_side_filter_label'             =>  $this->left_side_filter? "<span class='active_badge'>$this->left_side_filter</span>" : "<span class='inactive_badge'>0</span>",
            'detail_filter_label'             =>  $this->detail_filter ? "<span class='active_badge'>$this->detail_filter</span>" : "<span class='inactive_badge'>0</span>",
        ];
    }
}
