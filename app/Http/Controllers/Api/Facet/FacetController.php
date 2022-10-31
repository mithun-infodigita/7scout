<?php

namespace App\Http\Controllers\Api\Facet;

use App\Http\Controllers\Api\PartIndex\PartIndexController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Facet\StoreFacetRequest;
use App\Http\Requests\Api\Facet\UpdateFacetRequest;
use App\Http\Resources\Api\Facet\FacetCollectionResource;
use App\Http\Resources\Api\Facet\FacetSingleResource;
use App\Models\Facet\Facet;
use Illuminate\Http\Request;

class FacetController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(FacetCollectionResource::collection(Facet::all()->sortBy('order_column')));
    }

    public function store(StoreFacetRequest $request)
    {
        $facet = Facet::create($request->all());

        return response()->json(new FacetSingleResource($facet));
    }

    public function show(Request $request, Facet $facet)
    {
        return response()->json(new FacetSingleResource($facet));
    }

    public function update(UpdateFacetRequest $request, Facet $facet)
    {
        $facet->update($request->all());

        return response()->json(new FacetSingleResource($facet->fresh()));
    }

    public function updateOrder(Request $request)
    {
        $facets = Facet::all();
        $columns = $facets->pluck('column');
        $facets = $columns->pluck('name')->unique()->toArray();

        Facet::setNewOrder($request->facetIds);
    }

    public function destroy(Request $request, Facet $facet) {

        if($facet->delete()) {
            return response('success', 200);
        }
        else {
            return response(["message" => "Facet can't be deleted!"], 422);
        }
    }
}

