<?php

namespace App\Models\Producer;

use App\Models\DispatchLocation\DispatchLocation;
use App\Models\Import\Import;

/**
 * Class CompanyRelationship.
 */
trait ProducerRelationship
{
    public function imports()
    {
        return $this->hasMany(Import::class);
    }

    public function dispatchLocations()
    {
        return $this->belongsToMany(DispatchLocation::class);
    }
}
