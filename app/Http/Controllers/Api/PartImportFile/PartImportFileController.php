<?php

namespace App\Http\Controllers\Api\PartImportFile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Group\StoreGroupRequest;
use App\Http\Resources\Api\Group\GroupCollectionResource;
use App\Http\Resources\Api\Group\GroupSingleResource;

use App\Http\Resources\Api\Import\ImportCollectionResource;
use App\Http\Resources\Api\Import\ImportSingleResource;
use App\Models\Group\Group;
use App\Models\Import\Import;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PartImportFileController extends Controller
{
    public function index(Request $request)
    {
        $scripts = scandir(base_path('app/Imports/PartImports'));
        array_shift($scripts);
        array_shift($scripts);
        return $scripts;
    }
}

