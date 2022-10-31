<?php

namespace App\Http\Resources\Api\PartIndex;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class PartIndexCollectionResource extends JsonResource
{
    public $indexedParts;

    public function toArray($request)
    {
        return [
            'id'                        =>      $this->id,
            'name'                      =>      $this->name,
            'table_name'                =>      $this->table_name,
            'last_upload'               =>      $this->last_upload ? $this->last_upload->format('d.m.Y') : '',
            'crated_at'                 =>      $this->created_at->format('d.m.Y')
        ];
    }
}
