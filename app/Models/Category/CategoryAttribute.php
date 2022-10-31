<?php

namespace App\Models\Category;


/**
 * Trait CategoryAttribute.
 */
trait CategoryAttribute
{
    public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('categoryImage');
    }

    public function getDrawingUrlAttribute()
    {
        return $this->getFirstMediaUrl('categoryDrawing');
    }
}
