<?php

namespace App\Http\Controllers\Api\Import;

use App\Http\Controllers\Controller;

use App\Http\Requests\Api\Import\StoreImportPriceRuleRequest;
use App\Http\Requests\Api\Import\StoreImportruleRequest;
use App\Http\Resources\Api\ImportPriceRule\ImportPriceRuleCollectionResource;
use App\Http\Resources\Api\ImportPriceRule\ImportPriceRuleSingleResource;
use App\Models\Import\Import;
use App\Models\ImportPriceRule\ImportPriceRule;
use Illuminate\Http\Request;


class ImportPriceRuleController extends Controller
{
    public function index(Request $request, Import $import)
    {
       return response()->json(ImportPriceRuleCollectionResource::collection($import->importPriceRules->sortBy('order_column')));
    }

    public function store(StoreImportPriceRuleRequest $request, Import $import)
    {
        $request->merge(['import_id' => $import->id, 'status' => 'active']);

        $importPriceRule = ImportPriceRule::create($request->except('id'));

        return response()->json(new ImportPriceRuleSingleResource($importPriceRule));
    }
}

