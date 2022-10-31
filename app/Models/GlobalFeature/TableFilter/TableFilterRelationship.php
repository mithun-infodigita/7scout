<?php

namespace App\Models\GlobalFeature\TableFilter;

use App\Models\User\User;

trait TableFilterRelationship
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
