<?php

namespace App\Http\Resources\Api\Admin\Token;

use Illuminate\Http\Resources\Json\JsonResource;

class TokenCollectionResource extends JsonResource
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
            'created_at'            =>  $this->created_at ? $this->created_at->format('d.m.Y') : '',
            'last_used_at'          =>  $this->last_used_at ? $this->last_used_at->format('d.m.Y') : '',
        ];
    }
}
