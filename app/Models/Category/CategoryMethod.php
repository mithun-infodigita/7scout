<?php

namespace App\Models\Category;

/**
 * Trait CategoryMethod.
 */
trait CategoryMethod
{
    protected static function booted()
    {
        static::created(function ($category) {
            self::setCategoryLevel($category);
        });
    }

    public static function setCategoryLevel($category)
    {
        $level = 0;
        if($category->parent_id) {
            $level = Category::find($category->parent_id)->ancestorsAndSelf()->count();
        }

        $category->update([
            'level'     =>      $level
        ]);
    }
}
