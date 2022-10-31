<?php

namespace App\Models\Group;

use App\Models\Category\Category;
use App\Models\GroupColumn\GroupColumn;
use App\Models\ImportRule\ImportRule;

trait GroupRelationship
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function groupColumns()
    {
        return $this->hasMany(GroupColumn::class, 'group_id', 'id');
    }
}
