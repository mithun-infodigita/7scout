<?php

namespace App\Http\Controllers\Api\CustomDuty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomDutiesController extends Controller
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

