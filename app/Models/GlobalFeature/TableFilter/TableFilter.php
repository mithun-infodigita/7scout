<?php

namespace App\Models\GlobalFeature\TableFilter;

use Illuminate\Database\Eloquent\Model;

class TableFilter extends Model
{
    use TableFilterAttribute, TableFilterMethod, TableFilterRelationship, TableFilterScope;

    protected $fillable = [
        'name',
        'color',
        'user_id',
        'table_id',
        'default',
        'header_button',
        'filter_data'
    ];
}
