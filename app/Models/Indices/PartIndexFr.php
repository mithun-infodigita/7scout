<?php

namespace App\Models\Indices;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class PartIndexFr extends Model
{
    use Searchable;

    public function searchableAs()
    {
        if(env('INDEX_APPENDIX') !== null) {
            return 'part_index_fr_'.env('INDEX_APPENDIX');
        }

        return 'part_index_fr';
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();

        $array = $this->transform($array);

        $array['id'] = $this->part_id;
        $array['price'] =json_decode( $this->price);
        $array['stock'] = json_decode( $this->stock);
        $array['customs_tariff_numbers'] = json_decode( $this->customs_tariff_numbers);
        $array['part_specifications'] = json_decode( $this->part_specifications);
        $array['image_links'] = json_decode( $this->image_links);
        $array['image_link']    =  $this->table_detail_image_link;
        $array['updated_at_as_string']    =  $this->updated_at->toDateTimeString();
        $array['inline_pdf_url_pages'] = null;

        return $array;
    }

    public function getScoutKey()
    {
        return  $this->part_id;
    }

    protected $table = "part_index_fr";

    protected $guarded = [];

    protected $hidden = [

    ];

    protected $appends = [

    ];

    protected $with = [

    ];

    protected $casts = [

    ];



}
