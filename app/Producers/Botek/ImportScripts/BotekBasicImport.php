<?php


namespace App\Producers\Botek\ImportScripts;

use App\Models\CustomsSetting\CustomsSetting;

use App\Producers\Botek\Models\BotekPartsDe;

use App\Producers\Botek\Models\BotekPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class BotekBasicImport implements ToCollection, WithHeadingRow
{
    public $import;

    public $customsSettings;

    public $images;

    public function basicImport($import)
    {

        $this->images = scandir(public_path('/storage/producers/botek/partImages'));

        $this->customsSettings = CustomsSetting::all();

        $this->import = $import;

        $files = $import->getMedia();


        foreach ($files as $file) {

            if (file_exists($file->getPath())) {
                Excel::import($this, $file->getPath());
            }
        }
    }

    public function collection(Collection $rows)
    {
        $this->importParts($rows);
    }

    public function importParts($rows)
    {
        foreach ($rows as $row) {

            if(Str::of($row['artikel'])->trim()->isNotEmpty()) {
                switch ($this->import->language) {
                    case 'de':
                        $this->importPartsDe($row);
                        break;
                    case 'en':
                        $this->importPartsEn($row);
                        break;
                }
            }
        }
    }


    public function importPartsDe($row)
    {
//        var_dump($row);
//
//        exit;
        $row = $row->toArray();

        $partId = $this->import->producer->unique_id . '_' . $row['artikel'];

        $attributes = $this->getAttributesDe($row);

        if($row['artikel']) {
            BotekPartsDe::updateOrCreate(
                ['part_id' => $partId],
                $attributes
            );
        }
    }

    public function importPartsEn($row)
    {
        $row = $row->toArray();

        $partId = $this->import->producer->unique_id . '_' . $row['artikel'];

        $attributes = $this->getAttributesEn($row);

        if($row['artikel']) {
            BotekPartsEn::updateOrCreate(
                ['part_id' => $partId],
                $attributes
            );
        }
    }


    public function getAttributesDe($row)
    {
        $attributes = [
            'producer_id'                   =>  'botek',
            'producer_name'                 =>  'botek Präzisionsbohrtechnik GmbH',
            'part_number'                   =>  $row['artikel'],
            'part_name'                     =>  $row['artikelbezeichnung1'],
            'country_of_origin'             =>  $row['ursprungsland'] === 'D' ? 'DE' : null,
            'stock_part_id'                 =>  $row['artikel'],
            'reprocurement_time'            =>  21,
            'weight'                        =>  $row['gewicht'],
            'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
            'preferential_beneficiary_eu'   =>  $row['praefursprung'] === 'D' ? 1 : 0,
            'stock'                         =>  json_encode([ "12" =>  3]),
            'part_specifications'           =>  $this->getPartSpecifications($row),
            'coating'                       =>  $row['beschichtung'] !== 'unbeschichtet' ? $row['beschichtung'] : null,
            'shank_type'                    =>  $row['schaftausfuehrung'],
            'cutting_material'              =>  $row['hartmetall_sorte'],
            'table_detail_image_link'       =>  $this->getTableDetailImageLink($row),
            'image_links'                   =>  json_encode($this->getImageLinks($row)),
            'mapping_helper'                =>  trim(explode('Typ', $row['artikelbezeichnung1'] )[1]),
            'material_group'                =>  trim(explode('Typ', $row['artikelbezeichnung1'] )[1]),
            'discount_group'                =>  trim(explode('Typ', $row['artikelbezeichnung1'] )[1]),
            'sort_value'                    =>  $row['durchmesser_dc']
        ];

        return $attributes;
    }

    public function getAttributesEn($row)
    {

    }

    public function getPartSpecifications($row)
    {
        $type = trim(explode('Typ', $row['artikelbezeichnung1'] )[1]);

        $partSpecifications = [];

        $partSpecifications['dc']    =  array_key_exists('durchmesser_dc', $row) && $row['durchmesser_dc'] ? number_format($row['durchmesser_dc'] , 2)." mm" : null;
        $partSpecifications['oal']    =  array_key_exists('gesamtlaenge_oal', $row) && $row['gesamtlaenge_oal']? number_format($row['gesamtlaenge_oal'] , 2)." mm" : null;
        if(array_key_exists('spannutlaenge_lcf', $row) && $row['spannutlaenge_lcf']) {
            $partSpecifications['lcf'] = number_format(floatval($row['spannutlaenge_lcf']), 2) . " mm";
        }

        if(array_key_exists('sickenlaenge_lcf', $row) && $row['sickenlaenge_lcf']) {
            $partSpecifications['lcf'] = number_format(floatval($row['sickenlaenge_lcf']), 2) . " mm";
        }

        $partSpecifications['dcon']    =  array_key_exists('schaftdurchmesser_dcon', $row) && $row['schaftdurchmesser_dcon']? number_format($row['schaftdurchmesser_dcon'] , 2)." mm" : null;
        $partSpecifications['Anschliff']    =  array_key_exists('anschliff', $row) && $row['anschliff']? $row['anschliff'] : null;

        if(in_array($type, ['110', '113', '113HP'])) {
            $partSpecifications['Einspannhülse']    =  array_key_exists('artikelbezeichnung3', $row) && $row['artikelbezeichnung3']? $row['artikelbezeichnung3'] : null;
        }

        return json_encode(array_filter($partSpecifications));
    }


    public function getTableDetailImageLink($row)
    {
        $link = Arr::first($this->images, function ($value, $key) use ($row) {
            $type = trim(explode('Typ', $row['artikelbezeichnung1'] )[1]);
            $type = Str::of($type)->replace('HP', '_hp');
            if(str_contains($value, $type.'.') || str_contains($value, $type.'_')){
                return $value;
            }
        });

        if($link) {
            return env('APP_URL').'/storage/producers/botek/partImages/'.$link;
        }
    }

    public function getImageLinks($row)
    {
        $imageLinks = [];
        foreach ($this->images as $image)
        {
            $type = trim(explode('Typ', $row['artikelbezeichnung1'] )[1]);
            $type = Str::of($type)->replace('HP', '_hp');
            if(str_contains($image, $type.'.') || str_contains($image, $type.'_')){
                array_push($imageLinks, env('APP_URL').'/storage/producers/botek/partImages/'.$image);
            }

        }
        return $imageLinks;
    }


    public function getCustomsTariffNumbers($row)
    {
        $tariffNumber = $row['zolltarifnr'];
        $customSetting = $this->customsSettings->where('customs_tariff_number_ch', $tariffNumber)->first();

        return [
            'EU'    =>  $tariffNumber,
            'CH'    =>  $customSetting ? $customSetting->customs_tariff_number_ch : null,
        ];
    }


}
