<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Category\CategorySingleResource;
use App\Jobs\Category\CacheCategoryJob;
use App\Models\Category\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryImageController extends Controller
{
    public function store(Request $request, Category $category)
    {
        $medias = $category->getMedia('categoryImage');

        foreach ($medias as $media) {
            $media->delete();
        }

        $category->addMediaFromRequest('files')->toMediaCollection('categoryImage');

        CacheCategoryJob::dispatch();

        return response()->json(new CategorySingleResource($category->fresh()));
    }

    public function destroy(Request $request, Category $category)
    {
        $medias = $category->getMedia('categoryImage');

        foreach ($medias as $media) {
            $media->delete();
        }

        CacheCategoryJob::dispatch();

        return response()->json(new CategorySingleResource($category->fresh()));
    }
}

