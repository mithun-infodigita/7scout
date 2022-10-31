<?php

namespace App\Http\Controllers\Api\Ext\Category;

use App\Http\Controllers\Api\Category\CacheCategoryController;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Category\CategorySingleTreeResource;
use App\Http\Resources\Api\Ext\Category\CategoryCollectionResource;
use App\Http\Resources\Api\Ext\Category\CategoryCollectionTreeResource;
use App\Http\Resources\Api\Ext\Category\CategorySingleResource;
use App\Jobs\Category\CacheCategoryJob;
use App\Models\Category\Category;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Cache;

class CategoryController extends Controller
{
    public function index(Request $request)
    {

        if($request->input('type') === 'top') {
            return response()->json(CategoryCollectionResource::collection(Category::where('parent_id', 0)->get()));
        }

        if($request->input('type') === 'tree') {

            $categories = Cache::get('categories');

            if($categories) {

                return response($categories)->header('Content-Type', 'application/json');
            }

            else {

                CacheCategoryJob::dispatch();

                $categories = Category::where('parent_id', 0)->get();

                $categories->load('categories');
                $categories->load('facets');

                return response()->json(CategoryCollectionTreeResource::collection($categories));
            }

        }

        if($request->input('type') === 'flat') {
            return response()->json(CategoryCollectionResource::collection(Category::orderBy('name')->get()));
        }
    }

    public function show(Request $request, Category $category)
    {
        return response()->json(new CategorySingleResource($category->fresh()));
    }
}

