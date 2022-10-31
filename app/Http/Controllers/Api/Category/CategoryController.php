<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\Category\StoreCategoryRequest;
use App\Http\Requests\Api\Category\UpdateCategoryRequest;
use App\Http\Resources\Api\Category\CategoryCollectionResource;
use App\Http\Resources\Api\Category\CategoryCollectionSelectDropDownResource;
use App\Http\Resources\Api\Category\CategoryCollectionTreeResource;
use App\Http\Resources\Api\Category\CategoryCollectionTreeTableResource;
use App\Http\Resources\Api\Category\CategorySingleResource;
use App\Http\Resources\Api\Category\CategorySingleTreeResource;
use App\Jobs\Category\CacheCategoryJob;
use App\Models\Category\Category;
use App\Models\Group\Group;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if($request->input('type') === 'top') {
            return response()->json(CategoryCollectionResource::collection(Category::where('parent_id', 0)->get()));
        }

        if($request->input('type') === 'tree') {
            $categories = Category::where('parent_id', 0)->get();
            $categories->load('categories');
            return response()->json(CategoryCollectionTreeResource::collection($categories));
        }

        if($request->input('type') === 'flat') {
            return response()->json(CategoryCollectionResource::collection(Category::orderBy('name')->get()));
        }

        if($request->input('type') === 'flatOrderById') {
            return response()->json(CategoryCollectionResource::collection(Category::orderBy('id')->get()));
        }

        if($request->input('type') === 'treeTable') {
            $categories = Category::where('parent_id', 0)->orderBy('order_column')->get();
            $categories->load('categories');
            return response()->json(CategoryCollectionTreeTableResource::collection($categories));
        }

        if($request->input('type') === 'selectDropDown') {
            return response()->json(CategoryCollectionSelectDropDownResource::collection(Category::where('parent_id', 0)->get()));
        }
    }

    public function store(StoreCategoryRequest $request)
    {
        foreach (config('language') as $language ) {
            $lang = $language['key'];
            $request->merge([$lang => $request->translations[$language['key']]]);
        }

        $category = Category::create($request->except('translations'));

        Group::create([
            'name' => $category->name,
            'de' => $category->de,
            'en' => $category->en,
            'fr' => $category->fr,
            'it' => $category->it,
            'category_id' => $category->id
        ]);

        CacheCategoryJob::dispatch();

        return response()->json(new CategorySingleResource($category->fresh()));
    }

    public function show(Request $request, ... $categoryName)
    {

        //todo change the orWhere queries in a function related to config.language
        $category = Category::where('id', $categoryName)
            ->orWhere('name', $categoryName)
            ->orWhere('de', $categoryName)
            ->orWhere('en', $categoryName)
            ->orWhere('fr', $categoryName)
            ->orWhere('it', $categoryName)
            ->first();

        if($request->input('type') === 'tree') {
            return response()->json(new CategorySingleTreeResource($category->fresh()));
        }

        return response()->json(new CategorySingleResource($category->fresh()));
    }

    public function update(UpdateCategoryRequest $request, Category  $category)
    {
        $category->update($request->only('name', 'de', 'en', 'fr', 'it', 'parent_id'));

        Category::setCategoryLevel($category);

        $category->facets()->sync($request->facet_ids);

        CacheCategoryJob::dispatch();

        //CacheCategoryController::updateSingleCategoryCache($category);

        return response()->json(new CategorySingleResource($category->fresh()));
    }

    public function destroy(Request $request, Category  $category)
    {
        if($category->importRules->count() === 0) {
            $category->delete();

            CacheCategoryJob::dispatch();

            return response('success', 200);
        }
        else {
            return response(["message" => "Category can't be deleted!"], 422);
        }
    }

    public function updateOrder(Request $request, Category  $category)
    {
        $category->update($request->only('parent_id'));

        Category::setNewOrder($request->order_ids);

        CacheCategoryJob::dispatch();

        return response()->json(new CategorySingleResource($category->fresh()));
    }

}

