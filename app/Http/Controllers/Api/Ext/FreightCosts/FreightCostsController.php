<?php

namespace App\Http\Controllers\Api\Ext\FreightCosts;

use App\Http\Controllers\Controller;
use App\Models\Indices\PartIndexDe;
use Illuminate\Http\Request;

class FreightCostsController extends Controller
{
    public function index(Request $request)
    {
        $grossWeight = 0;

        $payload = json_decode($request->getContent());

        $freightCosts = collect(config('freightCosts'));

        $freightCosts = $freightCosts->where('sender_country', $payload->sender_country)->where('receiver_country', $payload->receiver_country)->first();

        foreach ($payload->parts as $item) {
            $part = PartIndexDe::where('part_id', $item->part_id)->first();
            $grossWeight += $part->weight * $item->quantity;
        }

        if($grossWeight > $freightCosts['min_weight']) {
            return [
                "value"     =>  $this->getWeightFee($grossWeight, $freightCosts),
                "currency"  =>  "CHF"
            ];
        }
        else {
            return [
                "value"     =>  $this->getMinWeightFee($grossWeight, $freightCosts),
                "currency"  =>  "CHF"
            ];
        }
    }

    public function getWeightFee($grossWeight, $freightCosts)
    {
        $totalFee = 0;

        $additionalWeight = $grossWeight - $freightCosts['min_weight'];

        $totalFee += $freightCosts['min_weight_fee'];

        $totalFee += $additionalWeight * $freightCosts['additional_weight_fee'];

        $totalFee += $freightCosts['stop_fee'];

        if($freightCosts['fuel_surcharge'] > 0) {
            $totalFee = $totalFee + $totalFee * $freightCosts['fuel_surcharge'] / 100;
        }

        $totalFee += $freightCosts['import_customs_fee'];

        return $totalFee;
    }

    public function getMinWeightFee($grossWeight, $freightCosts)
    {
        $totalFee = 0;

        $totalFee += $freightCosts['min_weight_fee'];

        $totalFee += $freightCosts['stop_fee'];

        if($freightCosts['fuel_surcharge'] > 0) {
            $totalFee = $totalFee + $totalFee * $freightCosts['fuel_surcharge'] / 100;
        }

        $totalFee += $freightCosts['import_customs_fee'];

        return $totalFee;
    }
}

