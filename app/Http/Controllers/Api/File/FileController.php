<?php

namespace App\Http\Controllers\Api\File;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\File\FileCollectionResource;
use App\Models\Import\Import;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FileController extends Controller
{

    public function extract(Request $request, Import $import, ... $id)
    {
        $file = $import->getMedia()->where('id', $id[0])->first();
    }
}

