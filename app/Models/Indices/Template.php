<?php

namespace App\Models\Indices;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Template extends Model
{
    use Searchable;

    public function searchableAs()
    {
        if(env('APP_ENV') !== 'production') {
            return 'test_index_name';
        }

        return 'index_name';
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();

        $array = $this->transform($array);

        $array['price'] =json_decode( $this->price);
        $array['stock'] = json_decode( $this->stock);

        return $array;
    }


    protected $table = "part_index_table";

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
