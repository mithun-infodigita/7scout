<?php

namespace App\Models\DispatchLocation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DispatchLocation.
 */
class DispatchLocation extends Model
{
    use SoftDeletes,
        DispatchLocationAttribute,
        DispatchLocationMethod,
        DispatchLocationRelationship,
        DispatchLocationScope;

    protected $fillable = [
        'id',
        'unique_id',
        'name',
        'street',
        'zip',
        'city',
        'country',
        'region',
    ];


    protected $hidden = [];

    protected $dates = [ 'deleted_at'];

    protected $with = [];

    protected $appends = [];

    protected $casts = [];

}
