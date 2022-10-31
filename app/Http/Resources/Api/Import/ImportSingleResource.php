<?php

namespace App\Http\Resources\Api\Import;

use App\Http\Resources\Api\File\FileCollectionResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ImportSingleResource extends JsonResource
{

    public function toArray($request)
    {

        $pdfs = scandir(public_path('/storage/producers/'.$this->producer->unique_id.'/pdfs'));

        $pdfs = array_values(Arr::except($pdfs , [0,1]));


        return [
            'id'                        =>      $this->id,
            'name'                      =>      $this->name,
            'producer_id'               =>      $this->producer_id,
            'producer_name'             =>      $this->producer->name,
            'status'                    =>      $this->status,
            'status_label'              =>      $this->getStatusLabel($this->status),
            'part_import_file'          =>      $this->part_import_file,
            'language'                  =>      $this->language,
            'updated_at'                =>      $this->updated_at->format('d.m.Y'),
            'created_at'                =>      $this->created_at->format('d.m.Y'),
            'one_to_one'                =>      json_decode($this->one_to_one),
            'category_mapping'          =>      json_decode($this->category_mapping),
            'price_mapping'             =>      json_decode($this->price_mapping),
            'pdf_mapping'               =>      json_decode($this->pdf_mapping) ,
            'pdfs'                      =>      $pdfs
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
