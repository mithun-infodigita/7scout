<?php

namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;

class Translation extends Model
{
    use TranslationAttribute,
        TranslationMethod,
        TranslationRelationship,
        TranslationScope;

    protected $fillable = [
        'id',
        'de',
        'en',
        'fr',
        'it',
        'translation_type_de',
        'translation_type_en',
        'translation_type_fr',
        'translation_type_it',
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
