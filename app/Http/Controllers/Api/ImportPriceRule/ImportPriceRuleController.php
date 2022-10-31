<?php

namespace App\Http\Controllers\Api\ImportPriceRule;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ImportPriceRule\ImportPriceRuleSingleResource;
use App\Models\ImportPriceRule\ImportPriceRule;
use App\Models\ImportRule\ImportRule;
use Illuminate\Http\Request;


class ImportPriceRuleController extends Controller
{

    public function show(Request $request, ImportPriceRule $importPriceRule) {
        return response()->json(new ImportPriceRuleSingleResource($importPriceRule));
    }

    public function update(Request $request, ImportPriceRule $importPriceRule)
    {
        $importPriceRule->update($request->all());
        return response()->json(new ImportPriceRuleSingleResource($importPriceRule->fresh()));
    }

    public function updateOrder(Request $request)
    {
        ImportPriceRule::setNewOrder($request->orderedIds);
    }

    public function destroy(Request $request, ImportPriceRule $importPriceRule) {

        if($importPriceRule->delete()) {
            return response('success', 200);
        }
        else {
            return response(["message" => "Import price rule can't be deleted!"], 422);
        }
    }
}

