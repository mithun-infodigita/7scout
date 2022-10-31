<?php

namespace App\Models\ImportPriceRule;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;


class ImportPriceRule extends Model implements Sortable
{
    use ImportPriceRuleAttribute,
        ImportPriceRuleMethod,
        ImportPriceRuleRelationship,
        ImportPriceRuleScope,
        SortableTrait,
        SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'import_id',
        'source_reference_column_id',
        'reference_compare_type',
        'map_reference_script',
        'country',
        'currency',
        'map_file_id',
        'source_price_column_name',
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
