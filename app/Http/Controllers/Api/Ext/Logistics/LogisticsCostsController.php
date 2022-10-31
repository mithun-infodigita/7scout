<?php

namespace App\Http\Controllers\Api\Ext\Logistics;

use App\Http\Controllers\Api\Ext\CustomsCosts\CustomsCostsController;
use App\Http\Controllers\Api\Ext\FreightCosts\FreightCostsController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogisticsCostsController extends Controller
{
    public function index(Request $request)
    {

        $freightCostsController = new FreightCostsController();
        $freightCosts = $freightCostsController->index($request);

        $customsCostsController = new CustomsCostsController();
        $customsCosts = $customsCostsController->index($request);

        return response()->json($this->getCosts($freightCosts, $customsCosts));
    }

    public function getCosts($freightCosts, $customCosts)
    {
        return [
            "freight"   =>  $freightCosts,
            "customs"   =>  $customCosts
        ];
    }
}
