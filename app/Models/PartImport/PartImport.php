<?php

namespace App\Models\PartImport;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class PartImport extends Model
{
    use PartImportAttribute,
        PartImportMethod,
        PartImportRelationship,
        PartImportScope;

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
