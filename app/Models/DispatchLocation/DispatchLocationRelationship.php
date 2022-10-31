<?php

namespace App\Models\DispatchLocation;

use App\Models\Producer\Producer;

/**
 * Class CompanyRelationship.
 */
trait DispatchLocationRelationship
{
    public function producers()
    {
        return $this->belongsToMany(Producer::class);
    }

}
