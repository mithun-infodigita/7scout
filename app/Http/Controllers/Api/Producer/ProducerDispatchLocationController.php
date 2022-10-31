<?php

namespace App\Http\Controllers\Api\Producer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Producer\StoreProducerRequest;
use App\Http\Requests\Api\Producer\UpdateProducerRequest;
use App\Http\Resources\Api\DispatchLocation\DispatchLocationCollectionResource;
use App\Http\Resources\Api\Producer\ProducerCollectionResource;
use App\Http\Resources\Api\Producer\ProducerSingleResource;
use App\Models\Column\Column;
use App\Models\Producer\Producer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProducerDispatchLocationController extends Controller
{
    public function index(Request $request, Producer $producer)
    {
        return response()->json(DispatchLocationCollectionResource::collection($producer->dispatchLocations));
    }


}
