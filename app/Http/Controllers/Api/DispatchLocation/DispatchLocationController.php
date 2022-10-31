<?php

namespace App\Http\Controllers\Api\DispatchLocation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DispatchLocation\StoreDispatchLocationRequest;
use App\Http\Requests\Api\DispatchLocation\UpdateDispatchLocationRequest;
use App\Http\Resources\Api\DispatchLocation\DispatchLocationCollectionResource;
use App\Http\Resources\Api\DispatchLocation\DispatchLocationSingleResource;
use App\Models\Column\Column;
use App\Models\DispatchLocation\DispatchLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class DispatchLocationController extends Controller
{
    public function index()
    {
        return response()->json(DispatchLocationCollectionResource::collection(DispatchLocation::all()));
    }

    public function show(Request $request, DispatchLocation $dispatchLocation)
    {
        return response()->json(new DispatchLocationSingleResource($dispatchLocation));
    }

    public function store(StoreDispatchLocationRequest $request)
    {
        $dispatchLocation = DispatchLocation::create($request->all());

        return response()->json(new DispatchLocationSingleResource($dispatchLocation->fresh()));
    }

    public function update(UpdateDispatchLocationRequest $request, DispatchLocation $dispatchLocation)
    {
        $dispatchLocation->update($request->only(['name']));

        return response()->json(new DispatchLocationSingleResource($dispatchLocation->fresh()));
    }
}
