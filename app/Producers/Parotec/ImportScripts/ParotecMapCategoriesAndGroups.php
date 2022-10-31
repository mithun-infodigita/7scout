<?php

namespace App\Producers\Parotec\ImportScripts;

use App\Models\Category\Category;
use App\Models\Group\Group;
use App\Producers\Parotec\Models\ParotecPartsDe;
use App\Producers\Parotec\Models\ParotecPartsEn;
use App\Producers\Parotec\Models\ParotecPartsFr;

class ParotecMapCategoriesAndGroups

{
    public function mapCategories($import)
    {
        switch ($import->language) {
            case 'de':
                $this->mapGermanData($import);
                break;
            case 'en':
                $this->mapEnglishData($import);
                break;
            case 'fr':
                $this->mapFrenchData($import);
                break;
            case 'it':
                $this->mapItalianData($import);
                break;
        }
    }

    public function mapGermanData($import) {
        foreach (json_decode($import->category_mapping) as $item) {
            $parts = ParotecPartsDe::where('mapping_helper', 'like', $item->validation_string.'%')->get();
            foreach ($parts as $part) {
                $part->update($this->categoryColumns($item, $import));;
            }
        }
    }

    public function mapEnglishData($import) {

        foreach (json_decode($import->category_mapping) as $item) {
            $parts = ParotecPartsEn::where('mapping_helper', 'like', $item->validation_string.'%')->get();
            foreach ($parts as $part) {
                $part->update($this->categoryColumns($item, $import));;
            }
        }
    }

    public function mapFrenchData($import) {
        foreach (json_decode($import->category_mapping) as $item) {
            $parts = ParotecPartsFr::where('mapping_helper', 'like', $item->validation_string.'%')->get();
            foreach ($parts as $part) {
                $part->update($this->categoryColumns($item, $import));;
            }
        }
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
