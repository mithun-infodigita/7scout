<?php

namespace App\Models\GroupColumn;

use App\Models\Column\Column;
use App\Models\Group\Group;

trait GroupColumnRelationship
{
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function column()
    {
        return $this->belongsTo(Column::class);
    }
}
