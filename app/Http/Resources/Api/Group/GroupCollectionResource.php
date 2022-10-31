<?php

namespace App\Http\Resources\Api\Group;

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
            'name'                  =>  $this->name,
            'de'                    =>  $this->de,
            'en'                    =>  $this->en,
            'fr'                    =>  $this->fr,
            'it'                    =>  $this->it,
            'active'                =>  $this->active,
            'active_label'                =>  $this->active ? "<span class='active_badge'>Aktiv</span>" : "<span class='inactive_badge'>Inaktiv</span>",
        ];
    }
}
