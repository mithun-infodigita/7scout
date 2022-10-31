<?php

namespace App\Models\Category;

use App\Models\Facet\Facet;
use App\Models\Group\Group;
use App\Models\ImportRule\ImportRule;

trait CategoryRelationship
{
    public function categories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function group()
    {
        return $this->hasOne(Group::class);
    }

    public function facets()
    {
        return $this->belongsToMany(Facet::class);
    }
}
