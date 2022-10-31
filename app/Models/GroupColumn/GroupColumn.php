<?php

namespace App\Models\GroupColumn;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;

class GroupColumn extends Model
{
    use GroupColumnAttribute,
        GroupColumnMethod,
        GroupColumnRelationship,
        GroupColumnScope,
        SortableTrait;

    protected $fillable = [
        'id',
        'group_id',
        'column_id',
        'name',
        'de_table',
        'en_table',
        'fr_table',
        'it_table',
        'de_detail',
        'en_detail',
        'fr_detail',
        'it_detail',
        'show_in_table',
        'show_in_table_detail',
        'show_in_detail_page',
        'left_side_filter',
        'detail_filter'
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
