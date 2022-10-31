<?php

namespace App\Models\User;


/**
 * Trait UserAttribute.
 */
trait UserAttribute
{
    public function getFullNameAttribute()
    {

        return $this->first_name." ".$this->last_name;
    }
}
