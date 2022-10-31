<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Category\CategorySingleTreeResource;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Category\Category;
use Illuminate\Http\Request;
use App\Http\Resources\Api\Ext\Category\CategoryCollectionTreeResource;
use Cache;
use Arr;

class CacheCategoryController extends Controller
{
    public function cacheCategoriesAsTree()
    {
        //Cache::forget('categories');

        $categories = Category::where('parent_id', 0)->get();

        Cache::forever('categories', json_encode(CategoryCollectionTreeResource::collection($categories)));
    }

    public static function updateSingleCategoryCache($category)
    {

//        $categories = Category::where('parent_id', 0)->get();
//
//        Cache::forever('categories', json_encode(CategoryCollectionTreeResource::collection($categories)));

        $categories = Cache::get('categories');


        if(!$categories) {
            $categories = Category::where('parent_id', 0)->get();

            Cache::forever('categories', json_encode(CategoryCollectionTreeResource::collection($categories)));
        }

        else {
            $categories = json_decode($categories);
//
//            var_dump($category->ancestorsAndSelf()->get()->pluck('id'));
//            var_dump($categories[0]);
//            exit;

            foreach ($categories as $key => $value) {
                if($category->id === $value->id) {
                    var_dump($key);
                   // $categories[$key] = new CategoryCollectionTreeResource($category);
                    break;
                }
            }

exit;
            Cache::forever('categories', json_encode($categories));

        }
    }

    //public static function

}

