<?php

namespace App\Http\Controllers\Api\FreightCost;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FreightCostsController extends Controller
{
    public function index(Request $request)
    {
        $costs = [];
        foreach (json_decode($request->getContent(), true) as $item) {
            array_push($costs, rand(3,55));
        }
        return response()->json($costs);
    }


}

