<?php

namespace App\Http\Resources\Api\Column;

use Illuminate\Http\Resources\Json\JsonResource;

class ColumnSingleResource extends JsonResource
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
            'type'                              =>  $this->type,
            'nullable'                          =>  $this->nullable,
            'nullable_label'                    =>  $this->nullable ? "<span class='active_badge'>Nullable</span>" : "<span class='inactive_badge'>Nullable</span>",
            'import_parts_table'                =>  $this->import_parts_table,
            'import_parts_table_label'          =>  $this->import_parts_table ? "<span class='active_badge'>Import parts table</span>" : "<span class='inactive_badge'>Import parts table</span>",
            'index_table_label'                 =>  $this->index_table ? "<span class='active_badge'>Index table</span>" : "<span class='inactive_badge'>Index table</span>",
            'index_table'                       =>  $this->index_table,
            'order_column'                      =>  $this->order_column,
            'default_de_name'               =>  $this->default_de_name,
            'default_en_name'              =>  $this->default_en_name,
            'default_fr_name'               =>  $this->default_fr_name,
            'default_it_name'              =>  $this->default_it_name,
            'default_show_in_frontend'             =>  $this->default_show_in_frontend,
            'default_show_in_table'                =>  $this->default_show_in_table,
            'default_show_in_table_detail'         =>  $this->default_show_in_table_detail,
            'default_show_in_detail_page'          =>  $this->default_show_in_detail_page,
            'default_show_in_frontend_label'       =>  $this->default_show_in_frontend ? "<span class='active_badge'>Frontend</span>" : "<span class='inactive_badge'>Frontend</span>",
            'default_show_in_table_label'          =>  $this->default_show_in_table ? "<span class='active_badge'>Table</span>" : "<span class='inactive_badge'>Table</span>",
            'default_show_in_table_detail_label'   =>  $this->default_show_in_table_detail ? "<span class='active_badge'>Table detail</span>" : "<span class='inactive_badge'>Table detail</span>",
            'default_show_in_detail_page_label'    =>  $this->default_show_in_detail_page ? "<span class='active_badge'>Detail page</span>" : "<span class='inactive_badge'>Detail page</span>"
        ];
    }
}
