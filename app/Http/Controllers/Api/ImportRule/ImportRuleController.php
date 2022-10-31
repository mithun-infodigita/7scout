<?php

namespace App\Http\Controllers\Api\ImportRule;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ImportRule\ImportRuleSingleResource;
use App\Models\ImportRule\ImportRule;
use Illuminate\Http\Request;


class ImportRuleController extends Controller
{

    public function show(Request $request, ImportRule $importRule) {
        return response()->json(new ImportRuleSingleResource($importRule));
    }

    public function update(Request $request, ImportRule $importRule)
    {
        $importRule = $importRule->update($request->all());
        return $importRule;
    }

    public function updateOrder(Request $request)
    {
        ImportRule::setNewOrder($request->orderedIds);
    }

    public function destroy(Request $request, ImportRule $importRule) {

        if($importRule->delete()) {
            return response('success', 200);
        }
        else {
            return response(["message" => "Import rule can't be deleted!"], 422);
        }
    }
}

