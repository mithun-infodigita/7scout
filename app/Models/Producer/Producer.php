<?php

namespace App\Models\Producer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class Producer.
 */
class Producer extends Model implements HasMedia
{
    use SoftDeletes,
        ProducerAttribute,
        ProducerMethod,
        ProducerRelationship,
        ProducerScope,
        InteractsWithMedia;

    protected $fillable = [
        'id',
        'unique_id',
        'name',
        'street',
        'zip',
        'city',
        'country',
        'region',
        'active'
    ];


    protected $hidden = [];

    protected $dates = [ 'deleted_at'];

    protected $with = [];

    protected $appends = [];

    protected $casts = [];

}
