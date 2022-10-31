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
use Illuminate\Support\Arr;

class ParcelCalculationController extends Controller
{
    public $partIndex, $payload, $senderCountry, $receiverCountry, $shippingPartners, $parts, $partsFromIndex, $singleParts, $totalWeight;

    public function __construct($partIndex, $payload)
    {
        $this->partIndex = $partIndex;
        $this->payload = $payload;
        $this->senderCountry = $payload->sender_country;
        $this->receiverCountry = $payload->receiver_country;
        $this->parts = $payload->parts;
    }

    public function getParcelsAndCosts()
    {
        $this->getSingleParts();

        $this->getShippingPartners();

        $this->getTotalWeight();

       $parcelsAndCostsByPartners = $this->getParcelsAndCostsByPartners();

//       var_dump($parcelsAndCostsByPartners);
//       exit;

        return $this->getPartnerWithBestPrice($parcelsAndCostsByPartners);
    }


    public function getPartnerWithBestPrice($shippers)
    {
        $shipperId = null;
        $shippingCosts = 999999;


        foreach ($shippers as $shipper => $value) {
            if($value['shipping_costs'] < $shippingCosts) {
                $shipperId = $shipper;
                $shippingCosts = $value['shipping_costs'];
            }
        }

        return $shippers[$shipperId];
    }

    public function getSingleParts()
    {
        $partIndex = new $this->partIndex();

        $senderCountry = $this->payload->sender_country === 'CH' ? 'CH' : 'EU';

        $this->partsFromIndex = collect();

        $this->singleParts = collect();

        foreach ($this->parts as $part) {

            $partFromIndex = $partIndex::where('part_id',$part->part_id)->first();

            $this->partsFromIndex = $this->partsFromIndex->merge($partFromIndex);
            for($i = 1;  $i <= $part->quantity; $i++) {
                $this->singleParts = $this->singleParts->merge([
                    $i => [
                        'part_name'   =>  $partFromIndex->part_name,
                        'part_id'   =>  $partFromIndex->part_id,
                        'weight'    =>  $partFromIndex->weight,
                        'customs_tariff_numbers' => json_decode($partFromIndex->customs_tariff_numbers)->$senderCountry,
                        'value'     =>  $part->price,
                        'country_of_origin'     =>  $partFromIndex->country_of_origin
                    ]
                ]);
            }

        }
    }

    public function getParcelsAndCostsByPartners()
    {
        $parcelsAndCostsByPartner = [];

        foreach ($this->shippingPartners as $shipper)
        {
            $parcelsAndCostsByPartner[$shipper['shipper_id']] = $this->getParcelsAndCostsByPartner(collect($shipper));
        }

        return $parcelsAndCostsByPartner;
    }

    public function getParcelsAndCostsByPartner($shipper)
    {
        if($this->totalWeight <= $shipper['parcel_max_weight']) {
            return $this->getSingleParcelWeightAndCosts($shipper);
        }

        return $this->getMultipleParcelWeightAndCosts($shipper);
    }

    public function getMultipleParcelWeightAndCosts($shipper)
    {
        $parcelMaxWeight = $shipper['parcel_max_weight'];
        $singleParcelWeightAndCosts['shipper'] = $shipper['shipper_id'];
        $singleParcelWeightAndCosts['currency']   = $shipper['currency'];

        $singleParts = collect($this->singleParts);

        $parcelId = 1;
        $parcelWeights = [];

        while($singleParts->count() >= 1) {
            $parcelWeights[$parcelId] = 0;
            $parcelParts[$parcelId] = array();
            $partQuantity[$parcelId] = 1;
            foreach ($singleParts as $key => $singlePart) {

                $weight = $parcelWeights[$parcelId] + $singlePart['weight'];
                if($weight < $parcelMaxWeight) {
                    $parcelWeights[$parcelId] += $singlePart['weight'];
                    array_push($parcelParts[$parcelId],  [
                        'description'           => $singlePart['part_name'],
                        'customs_tariff_numbers' => $singlePart['customs_tariff_numbers'],
                        'part_id'                => $singlePart['part_id'],
                        'weight'                 => $singlePart['weight'],
                        'value'                  => $singlePart['value'],
                        'country_of_origin'     =>  $singlePart['country_of_origin']
                    ]);

                    $singleParts->forget($key);
                }
                $partQuantity[$parcelId]++;
            }

            $parcelId++;
        }

        $parcels =  $this->getParcelWeightAndCosts($parcelWeights, $shipper, $parcelParts);
        $singleParcelWeightAndCosts['parcels'] = $parcels;
        $singleParcelWeightAndCosts['shipping_costs'] = $this->getTotalShippingPrice($parcels);
        $singleParcelWeightAndCosts['customs_administration'] = $this->getMultipleCustomsAdministration($parcelParts, $shipper);
        return $singleParcelWeightAndCosts;
    }

    public function getMultipleCustomsAdministration($parcels, $shipper)
    {
        $customsAdministrations = 0;

        foreach ($parcels as $parcel) {

            $customsAdministrations += $shipper['customs_administration'];

            $numAdditionalCustomTariffNumbers = collect($parcel)->unique()->count() - $shipper['included_custom_tariff_number'];

            if($numAdditionalCustomTariffNumbers > 0) {
                $customsAdministrations += $numAdditionalCustomTariffNumbers * $shipper['additional_tariff_number_costs'];
            }
        }

        return $customsAdministrations;
    }

    public function getTotalShippingPrice($parcels)
    {
        $totalShippingPrice = 0;

        foreach ($parcels as $parcel) {

            $totalShippingPrice += $parcel['costs'];
        }

        return $totalShippingPrice;
    }
    public function getParcelWeightAndCosts($parcels, $shipper, $parcelParts)
    {
        $parcel = [];
        foreach ($parcels as $key => $value) {
            $parcel[$key]['weight'] = $value;
            foreach ($shipper['weight_and_costs'] as $weightAndCosts) {
                if($value < $weightAndCosts['max_net_weight']) {
                    $parcel[$key]['costs'] = $weightAndCosts['shipping_costs'];
                    $parcel[$key]['parts'] = $parcelParts[$key];
                }
            }

            $positions = [];
            foreach ($parcel[$key]['parts'] as $part) {
                if(array_key_exists($part['part_id'], $positions)) {
                    $positions[$part['part_id']]['net_weight']  += $part['weight'];
                    $positions[$part['part_id']]['value']  += $part['value'];
                }
                else {
                    $positions[$part['part_id']] = $part;
                    $positions[$part['part_id']]['net_weight']  = $part['weight'] ;
                    $positions[$part['part_id']]['value']  = $part['value'];
                }
            }
            $parcel[$key]['parts'] = $positions;
        }

        return $parcel;
    }

    public function getSingleParcelWeightAndCosts($shipper)
    {
        $singleParcelWeightAndCosts['shipper'] = $shipper['shipper_id'];
        $singleParcelWeightAndCosts['currency']   = $shipper['currency'];

        foreach ($shipper['weight_and_costs'] as $weightAndCost){
            if ($weightAndCost['max_net_weight'] > $this->totalWeight) {
                $singleParcelWeightAndCosts['gross_weight'] = $weightAndCost['gross_weight'];
                $singleParcelWeightAndCosts['shipping_costs'] = $weightAndCost['shipping_costs'];
            }
        }

        $positions = [];
        foreach ( collect($this->singleParts) as $part) {
            if(array_key_exists($part['part_id'], $positions)) {
                $positions[$part['part_id']]['net_weight']  += $part['weight'];
                $positions[$part['part_id']]['value']  += $part['value'];
            }
            else {
                $positions[$part['part_id']] = $part;
                $positions[$part['part_id']]['net_weight']  = $part['weight'] ;
                $positions[$part['part_id']]['value']  = $part['value'];
            }
        }
        $singleParcelWeightAndCosts['parts'] = $positions;

        $singleParcelWeightAndCosts['customs_administration'] = $this->getCustomsAdministration($shipper);

        return $singleParcelWeightAndCosts;
    }

    public function getCustomsAdministration($shipper)
    {
        $tariffNumbers = $this->singleParts->pluck('custom_tariff_numbers')->unique();
        $customsAdministrations = $shipper['customs_administration'];

        $numAdditionalCustomTariffNumbers = $tariffNumbers->count() - $shipper['included_custom_tariff_number'];

        if($numAdditionalCustomTariffNumbers > 0) {
            $customsAdministrations += $numAdditionalCustomTariffNumbers * $shipper['additional_tariff_number_costs'];
        }
        return $customsAdministrations;
    }

    public function getShippingPartners()
    {
        $allShippingPartners = collect(config('shippingCosts.shipping_shippers'));

        $this->shippingPartners = $allShippingPartners->where('sender_country', $this->senderCountry)
            ->where('receiver_country', $this->receiverCountry)
            ->where('max_total_weight', '>=', $this->getTotalWeight())
            ->where('single_part_max_weight', '>=', $this->getSinglePartMaxWeight());
    }

    public function getTotalWeight() {
        $this->totalWeight = 0;

        $partIndex = new $this->partIndex();

        foreach ($this->parts as $part) {
            $partFromIndex = $partIndex::where('part_id',$part->part_id)->first();
            $this->totalWeight += $partFromIndex->weight * $part->quantity;
        }

        return  $this->totalWeight;
    }

    public function getSinglePartMaxWeight() {
        $singlePartWeight = 0;

        $partIndex = new $this->partIndex();

        foreach ($this->parts as $part) {
            $partFromIndex = $partIndex::where('part_id',$part->part_id)->first();
            if($partFromIndex->weight > $singlePartWeight) {
                $singlePartWeight = $partFromIndex->weight;
            }
        }

        return $singlePartWeight;
    }
}
