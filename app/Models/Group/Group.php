<?php

namespace App\Models\Group;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use GroupAttribute,
        GroupMethod,
        GroupRelationship,
        GroupScope;

    protected $fillable = [
        'id',
        'name',
        'de',
        'en',
        'fr',
        'it',
        'category_id',
        'active'
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
