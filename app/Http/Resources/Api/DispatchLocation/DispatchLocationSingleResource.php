<?php

namespace App\Http\Resources\Api\DispatchLocation;

use Illuminate\Http\Resources\Json\JsonResource;

class DispatchLocationSingleResource extends JsonResource
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
            'unique_id'                 =>      $this->unique_id,
            'name'                      =>      $this->name,
            'street'                    =>      $this->street,
            'zip'                       =>      $this->zip,
            'city'                      =>      $this->city,
            'created_at'                =>      $this->created_at->format('d.m.Y'),
            'active'                    =>      $this->active,
            'active_label'              =>      $this->active ? "<span class='active_badge'>Active</span>" : "<span class='inactive_badge'>Inactive</span>",
        ];
    }
}
