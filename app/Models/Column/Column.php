<?php

namespace App\Models\Column;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;

class Column extends Model
{
    use ColumnAttribute,
        ColumnMethod,
        ColumnRelationship,
        ColumnScope,
        SortableTrait;

    protected $fillable = [
        'id',
        'name',
        'type',
        'nullable',
        'import_parts_table',
        'index_table',
        'unique',
        'order_column',
        'default_de_name',
        'default_en_name',
        'default_fr_name',
        'default_it_name',
        'default_show_in_frontend',
        'default_show_in_table',
        'default_show_in_table_detail',
        'default_show_in_detail_page'
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
