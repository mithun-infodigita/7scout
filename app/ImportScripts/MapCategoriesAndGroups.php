<?php

namespace App\ImportScripts;

use App\Models\Category\Category;
use App\Models\Group\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MapCategoriesAndGroups

{

    public function mapCategories($import)
    {
        $table = $import->producer->unique_id . '_parts_' . $import->language;

        DB::table($table)->chunkById(10000000, function ($parts) use ($table, $import) {

            foreach ($parts as $part) {

                $source = json_decode($part->full_record);

                foreach (json_decode($import->category_mapping) as $item) {

                    if (Str::contains(eval("return " . $item->map_script . ";"), $item->validation_string)) {
                        DB::table($table)
                            ->where('id', $part->id)
                            ->update($this->categoryColumns($item, $import));
                    }
                }

            }
        });
    }

    public function categoryColumns($item, $import)
    {
        $columns = [];

        $category = Category::find($item->category_id);

        $catLevel = $category->level + 1;

        $language = $import->language;

        $columns['cat_level_1_id'] = null;
        $columns['cat_level_1_name'] = null;
        $columns['cat_level_2_id'] = null;
        $columns['cat_level_3_name'] = null;
        $columns['cat_level_3_id'] = null;
        $columns['cat_level_3_name'] = null;
        $columns['cat_level_4_id'] = null;
        $columns['cat_level_4_name'] = null;
        $columns['cat_level_5_id'] = null;
        $columns['cat_level_5_name'] = null;


        $columns['cat_level_' . $catLevel . '_id'] = $category->id;

        if ($columns['cat_level_5_id']) {
            $columns['cat_level_5_name'] = Category::where('id', $columns['cat_level_5_id'])->first()->$language;
            $parentId = Category::where('id', $columns['cat_level_5_id'])->first()->parent_id;
            $parent = Category::where('id', $parentId)->first();
            $columns['cat_level_4_id'] = $parentId;
            $columns['cat_level_4_name'] = $parent->$language;
            $group = Group::where('category_id', $columns['cat_level_5_id'])->first();
            $columns['group_id'] = $group->id;
            $columns['group_name'] = $group->$language;
        }

        if ($columns['cat_level_4_id']) {
            $columns['cat_level_4_name'] = Category::where('id', $columns['cat_level_4_id'])->first()->$language;
            $parentId = Category::where('id', $columns['cat_level_4_id'])->first()->parent_id;
            $parent = Category::where('id', $parentId)->first();
            $columns['cat_level_3_id'] = $parentId;
            $columns['cat_level_3_name'] = $parent->$language;
            if (!array_key_exists('group_id', $columns)) {
                $group = Group::where('category_id', $columns['cat_level_4_id'])->first();
                $columns['group_id'] = $group->id;
                $columns['group_name'] = $group->$language;
            }
        }

        if ($columns['cat_level_3_id']) {
            $columns['cat_level_3_name'] = Category::where('id', $columns['cat_level_3_id'])->first()->$language;
            $parentId = Category::where('id', $columns['cat_level_3_id'])->first()->parent_id;
            $parent = Category::where('id', $parentId)->first();
            $columns['cat_level_2_id'] = $parentId;
            $columns['cat_level_2_name'] = $parent->$language;
            if (!array_key_exists('group_id', $columns)) {
                $group = Group::where('category_id', $columns['cat_level_3_id'])->first();
                $columns['group_id'] = $group->id;
                $columns['group_name'] = $group->$language;
            }
        }

        if ($columns['cat_level_2_id']) {
            $columns['cat_level_2_name'] = Category::where('id', $columns['cat_level_2_id'])->first()->$language;
            $parentId = Category::where('id', $columns['cat_level_2_id'])->first()->parent_id;
            $parent = Category::where('id', $parentId)->first();
            $columns['cat_level_1_id'] = $parentId;
            $columns['cat_level_1_name'] = $parent->$language;
            if (!array_key_exists('group_id', $columns)) {
                $group = Group::where('category_id', $columns['cat_level_2_id'])->first();
                $columns['group_id'] = $group->id;
                $columns['group_name'] = $group->$language;
            }
        }

        if (!array_key_exists('group_id', $columns)) {
            $group = Group::where('category_id', $columns['cat_level_1_id'])->first();
            $columns['group_id'] = $group->id;
            $columns['group_name'] = $group->$language;
        }

        return $columns;
    }

}
