<?php

namespace App\Models\Facet;

use App\Models\Category\Category;
use App\Models\Column\Column;
use App\Models\Group\Group;

trait FacetRelationship
{
    public function column()
    {
        return $this->belongsTo(Column::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
