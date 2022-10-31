<?php

namespace App\Http\Resources\Api\File;

use Illuminate\Http\Resources\Json\JsonResource;

class FileCollectionResource extends JsonResource
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
            'id'                =>      $this->id,
            'name'              =>      $this->name,
            'created_at'        =>      $this->created_at->format('d.m.Y'),
            'url'               =>      $this->getUrl(),
            'size'              =>      $this->human_filesize($this->size)
        ];
    }

   public function human_filesize($size, $precision = 0) {
        $units = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $step = 1024;
        $i = 0;
        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }
        return round($size, $precision).$units[$i];
    }

}
