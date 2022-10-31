<?php

namespace App\Imports\StandardImport\ImportScripts;

use App\Models\Category\Category;
use App\Models\Group\Group;
use DB;

class StandardMapCategoriesAndGroups


{
    public $import;

    public $datatable;

    public function mapCategories($import)
    {
        $this->import = $import;

        $this->setDataTable();

        $this->mapData($import);

        $this->mapDefinedCategories();
    }

    public function setDataTable()
    {
        $this->datatable = $this->import->producer->unique_id."_parts_".$this->import->language;
    }

    public function mapData($import)
    {

        $parts = null;
        foreach (json_decode($import->category_mapping) as $item) {

            switch ($item->validation_type) {
                case 'equal': $parts = DB::table($this->datatable)->where( $item->map_script, '=', $item->validation_string)->get();
                    break;

                case 'contains': $parts = DB::table($this->datatable)->where( $item->map_script, 'like', '%'.$item->validation_string.'%')->get();
                break;

                case 'between': $validationValues = explode(':', $item->validation_string);
                    $parts = DB::table($this->datatable)->where( $item->map_script, '<', $validationValues[0])
                        ->where( $item->map_script, '>', $validationValues[1])
                        ->get();
                    break;
            }

            if($parts) {
                foreach ($parts as $part) {
                    DB::table($this->datatable)
                        ->where('part_id', $part->part_id)
                        ->update($this->categoryColumns($item->category_id, $import));
                }
            }
        }
    }


    public function mapDefinedCategories()
    {
        $parts = DB::table($this->datatable)->get();

        foreach ($parts as $part) {
            if($part->cat_id) {
                DB::table($this->datatable)
                    ->where('part_id', $part->part_id)
                    ->update($this->categoryColumns($part->cat_id, $this->import));
            }
        }
    }

    public function categoryColumns($categoryId, $import)
    {
        $columns = [];

        $category = Category::find($categoryId);

        $catLevel = $category->level + 1;

        $language = $import->language;

        $columns['cat_level_1_id'] = null;
        $columns['cat_level_1_name'] = null;
        $columns['cat_level_2_id'] = null;
        $columns['cat_level_2_name'] = null;
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
