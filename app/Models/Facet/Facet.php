<?php

namespace App\Models\Facet;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\EloquentSortable\Sortable;

class Facet extends Model implements Sortable
{
    use FacetAttribute,
        FacetMethod,
        FacetRelationship,
        FacetScope,
        SortableTrait;

    protected $fillable = [
        'id',
        'column_id',
        'name',
        'de',
        'en',
        'fr',
        'it',
        'global_facet',
        'item_sort'
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
