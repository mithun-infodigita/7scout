<?php

namespace App\Http\Controllers\Api\Ext\Shipping;

use App\Http\Controllers\Api\Ext\CustomsCosts\CustomsCostsController;
use App\Http\Controllers\Api\Ext\FreightCosts\FreightCostsController;
use App\Http\Controllers\Controller;
use App\Models\Indices\PartIndexDe;
use App\Models\Indices\PartIndexEn;
use App\Models\Indices\PartIndexFr;
use App\Models\Indices\PartIndexIt;
use Illuminate\Http\Request;

class ShippingCostsController extends Controller
{
    public $partIndex;

    public function index(Request $request)
    {
        $payload = json_decode($request->getContent());

        $this->setPartIndex($payload->user_language);

        if($payload->express) {
            return 'Express not ready yet';
        }

        $totalWeight = $this->getTotalWeight($payload->parts);

//        var_dump('total weight');
//        var_dump($totalWeight);

        if($totalWeight >= config('shippingCosts.max_total_weight')) {
            return 'Total weight to too heavy for parcel shipment';
        }

        $maxSinglePartWeight = $this->getMaxSinglePartWeight($payload->parts);
//        var_dump('max single part weight');
//        var_dump($maxSinglePartWeight);


        if($maxSinglePartWeight >= config('shippingCosts.single_part_max_weight')) {
            return 'Single part weight is to too heavy for parcel shipment';
        }

        $parcelCalculationController = new ParcelCalculationController($this->partIndex, $payload);
        $parcels = $parcelCalculationController->getParcelsAndCosts();


        $totalPrice = $this->getTotalPrice($payload->parts);
        $customs = $this->getCustoms($totalPrice, $parcels['shipper']);

        //var_dump($parcels);
        return response()->json([
            'parcels'       =>  $parcels,
            'shipper'       =>  $parcels['shipper'],
            'freight'       =>  [
                'value'     =>  $parcels['shipping_costs'],
                'currency'  =>  $parcels['currency']
            ],

            'customs'       =>  [
                'value'     =>  $customs,
                'currency'  =>  $parcels['currency']
            ],

            'customs_administration'       =>  [
                'value'     =>  $parcels['customs_administration'],
                'currency'  =>  $parcels['currency']
            ]

        ]);
    }


    public function setPartIndex($userLanguage) {
        switch ($userLanguage) {
            case 'de':
                $this->partIndex = PartIndexDe::class;
                break;
            case 'en':
                $this->partIndex = PartIndexEn::class;
                break;
            case 'fr':
                $this->partIndex = PartIndexFr::class;
                break;
            case 'it':
                $this->partIndex = PartIndexIt::class;
                break;
            default:
                $this->partIndex = config('shippingCosts.part_index_fallback');
        }
    }

    public function getTotalWeight($parts) {
        $totalWeight = 0;

        $partIndex = new $this->partIndex();

        foreach ($parts as $part) {
            $partFromIndex = $partIndex::where('part_id',$part->part_id)->first();
            $totalWeight += $partFromIndex->weight * $part->quantity;
        }

        return $totalWeight;
    }

    public function getMaxSinglePartWeight($parts) {
        $singlePartWeight = 0;

        $partIndex = new $this->partIndex();

        foreach ($parts as $part) {
            $partFromIndex = $partIndex::where('part_id',$part->part_id)->first();
            if($partFromIndex->weight > $singlePartWeight) {
                $singlePartWeight = floatval($partFromIndex->weight);
            }
        }

        return $singlePartWeight;
    }

    public function getTotalPrice($parts) {
        $totalPrice = 0;

        foreach ($parts as $part) {

            $totalPrice += $part->price * $part->quantity;
        }

        return $totalPrice;
    }

    public function getCustoms($totalPrice, $shipperId)
    {
        $allShippingPartners = collect(config('shippingCosts.shipping_shippers'));

        $shipper = $allShippingPartners->where('shipper_id', $shipperId)->first();

        return $shipper['duties_percentage'] / 100 * $totalPrice;
    }


}
