<?php

namespace App\Http\Resources\Api\Import;

use Illuminate\Http\Resources\Json\JsonResource;

class ImportCollectionResource extends JsonResource
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
            'id'                        =>      $this->id,
            'name'                      =>      $this->name,
            'producer_id'               =>      $this->producer_id,
            'producer_name'             =>      $this->producer->name,
            'status'                    =>      $this->status,
            'status_label'              =>      $this->getStatusLabel($this->status),
            'language'                  =>      $this->language,
            'updated_at'                =>      $this->updated_at->format('d.m.Y'),
            'created_at'                =>      $this->created_at->format('d.m.Y'),
            'notification'              =>      $this->notification,
            'previous_import_name'      =>      $this->previousImport ? $this->previousImport->name : ''
        ];
    }

    public function getStatusLabel($status)
    {
        switch ($status) {
            case 'draft':
                return "<span class='warning_badge'>Draft</span>";
            case 'empty':
                return "<span class='grey_badge'>Empty</span>";
            case 'error':
                return "<span class='danger_badge'>Error</span>";
            case 'merging':
                return "<span class='warning_badge'>Merging</span>";
            case 'importing':
                return "<span class='warning_badge'>Importing</span>";
            case 'imported':
                return "<span class='green_lighten_badge'>Imported</span>";
            case 'merged':
                return "<span class='success_badge'>Merged</span>";
        }
    }
}
