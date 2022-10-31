<?php

namespace App\Http\Controllers\Api\Import;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\Import\StoreImportruleRequest;
use App\Http\Resources\Api\ImportRule\ImportRuleCollectionResource;
use App\Http\Resources\Api\ImportRule\ImportRuleSingleResource;
use App\Models\Import\Import;
use App\Models\ImportRule\ImportRule;
use Illuminate\Http\Request;


class ImportImportRuleController extends Controller
{
    public function index(Request $request, Import $import)
    {
       return response()->json(ImportRuleCollectionResource::collection($import->importRules->sortBy('order_column')));
    }

    public function store(Request $request, Import $import)
    {
        $request->merge(['import_id' => $import->id, 'status' => 'active']);

        $importRule = ImportRule::create($request->except('id'));

        return response()->json(new ImportRuleSingleResource($importRule));
    }
}

