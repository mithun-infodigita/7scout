<?php

namespace App\Models\Import;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Import extends Model implements HasMedia
{
    use ImportAttribute,
        ImportMethod,
        ImportRelationship,
        ImportScope,
        InteractsWithMedia;

    protected $fillable = [
        'id',
        'name',
        'producer_id',
        'language',
        'status',
        'notification',
        'one_to_one',
        'category_mapping',
        'price_mapping',
        'pdf_mapping'
    ];


    protected $hidden = [

    ];

    protected $appends = [

    ];

    protected $with = [

    ];

    protected $casts = [

    ];



}
