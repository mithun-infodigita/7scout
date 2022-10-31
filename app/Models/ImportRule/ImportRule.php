<?php

namespace App\Models\ImportRule;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;


class ImportRule extends Model implements Sortable
{
    use ImportRuleAttribute,
        ImportRuleMethod,
        ImportRuleRelationship,
        ImportRuleScope,
        SortableTrait,
        SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'import_id',
        'source_reference_column_id',
        'reference_compare_type',
        'map_reference_script',
        'map_column_id',
        'map_file_id',
        'map_value_script',
        'order_column',
        'status'
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
