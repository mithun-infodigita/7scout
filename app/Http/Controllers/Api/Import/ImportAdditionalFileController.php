<?php

namespace App\Http\Controllers\Api\Import;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\File\FileCollectionResource;
use App\Models\Import\Import;
use Illuminate\Http\Request;


class ImportAdditionalFileController extends Controller
{
    public function index(Request $request, Import $import)
    {
        $files = $import->getMedia('additionalFiles');

        return response()->json(FileCollectionResource::collection($files));
    }

    public function store(Request $request, Import $import)
    {
        $fileAdders = $import->addMultipleMediaFromRequest(['files'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection('additionalFiles');
            });
    }

    public function show(Request $request, Import $import, ... $id)
    {
        $file = $import->getMedia('additionalFiles')->where('id', $id[0])->first();

        return response()->download($file->getPath(), $file->file_name);
    }

    public function destroy(Request $request, Import $import, ... $id)
    {
        $file = $import->getMedia('additionalFiles')->where('id', $id[0])->first();

        if($file->delete()) {
            return response('success', 200);
        }
    }
}

