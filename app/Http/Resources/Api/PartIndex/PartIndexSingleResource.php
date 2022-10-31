<?php

namespace App\Http\Resources\Api\PartIndex;
use Illuminate\Http\Resources\Json\JsonResource;

class PartIndexSingleResource extends JsonResource
{

    public function toArray($request)
    {
        ini_set('memory_limit', '-1');

        $className = "App\Models\Indices\\".$this->model_name;
        $class = new $className();

        $indexedParts = $class::all();

        return [
            'id'                        =>      $this->id,
            'status'                    =>      $this->status,
            'status_label'              =>      $this->getStatuslabel($this->status),
            'name'                      =>      $this->name,
            'table_name'                =>      $this->table_name,
            'last_upload'               =>      $this->last_upload ? $this->last_upload->format('d.m.Y') : '',
            'crated_at'                 =>      $this->created_at->format('d.m.Y'),
            'num_indexed_parts'         =>      $indexedParts->count(),
            'cat_level_1_filled'        =>      $indexedParts->whereNotNull('cat_level_1')->count(),
            'cat_level_1_empty'         =>      $indexedParts->whereNull('cat_level_1')->count(),
            'cat_level_2_filled'        =>      $indexedParts->whereNotNull('cat_level_2')->count(),
            'cat_level_2_empty'         =>      $indexedParts->whereNull('cat_level_2')->count(),
            'cat_level_3_filled'        =>      $indexedParts->whereNotNull('cat_level_3')->count(),
            'cat_level_3_empty'         =>      $indexedParts->whereNull('cat_level_3')->count(),
            'cat_level_4_filled'        =>      $indexedParts->whereNotNull('cat_level_4')->count(),
            'cat_level_4_empty'         =>      $indexedParts->whereNull('cat_level_4')->count(),
            'cat_level_5_filled'        =>      $indexedParts->whereNotNull('cat_level_5')->count(),
            'cat_level_5_empty'         =>      $indexedParts->whereNull('cat_level_5')->count(),
            'group_filled'              =>      $indexedParts->whereNotNull('group')->count(),
            'group_empty'               =>      $indexedParts->whereNull('group')->count()
        ];
    }

    public function getStatusLabel($status)
    {
        switch ($status) {
            case 'empty':
                return "<span class='grey_badge'>Empty</span>";
            case 'inProgress':
                return "<span class='danger_badge'>In Progress</span>";
            case 'changed':
                return "<span class='warning_badge'>Changed</span>";
            case 'upToDate':
                return "<span class='success_badge'>Up to date</span>";
        }
    }
}
