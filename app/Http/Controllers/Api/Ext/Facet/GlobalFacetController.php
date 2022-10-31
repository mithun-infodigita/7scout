<?php

namespace App\Http\Controllers\Api\Ext\Facet;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Ext\Facet\FacetCollectionResource;
use App\Models\Facet\Facet;
use Illuminate\Http\Request;

class GlobalFacetController extends Controller
{
    public function index(Request $request)
    {
        $facets = Facet::where('global_facet', 1)->get();

        return response()->json(FacetCollectionResource::collection($facets));
    }


}

