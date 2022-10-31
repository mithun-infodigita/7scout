<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;


class Category extends Model  implements Sortable, HasMedia
{
    use CategoryAttribute,
        CategoryMethod,
        CategoryRelationship,
        CategoryScope,
        HasRecursiveRelationships,
        SortableTrait,
        InteractsWithMedia;

    protected $fillable = [
        'id',
        'level',
        'parent_id',
        'name',
        'de',
        'en',
        'fr',
        'it'
    ];


    protected $hidden = [
        'media'
    ];

    protected $appends = [
        'image_url',
        'drawing_url'
    ];

    protected $with = [
        'group'
    ];

    protected $casts = [

    ];



}
