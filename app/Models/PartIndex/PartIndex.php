<?php

namespace App\Models\PartIndex;

use Illuminate\Database\Eloquent\Model;

class PartIndex extends Model
{
    use PartIndexAttribute,
        PartIndexMethod,
        PartIndexRelationship,
        PartIndexScope;

    protected $table = 'part_indexes';

    protected $fillable = [
        'name',
        'status',
        'table_name',
        'model_name'
    ];


    protected $hidden = [

    ];

    protected $appends = [

    ];

    protected $with = [

    ];

    protected $casts = [

    ];

    protected $dates = [
        'created_at',
        'last_upload'
    ];



}
