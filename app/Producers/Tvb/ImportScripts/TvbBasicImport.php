<?php


namespace App\Producers\Tvb\ImportScripts;

use App\Models\CustomsSetting\CustomsSetting;

use App\Producers\Tvb\Models\TvbPartsDe;

use App\Producers\Tvb\Models\TvbPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;


class TvbBasicImport implements ToCollection, WithHeadingRow
{
    public $import;

    public $customsSettings;

    public $images;

    public function basicImport($import)
    {


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
            if(Str::of($row['artikelnummer'])->trim()->isNotEmpty()) {
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

        $row = $row->toArray();

        $partId = $this->import->producer->unique_id . '_' . $row['artikelnummer'];

        $attributes = $this->getAttributesDe($row);

        if($row['artikelnummer']) {
            TvbPartsDe::updateOrCreate(
                ['part_id' => $partId],
                $attributes
            );
        }
    }

    public function importPartsEn($row)
    {
        $row = $row->toArray();

        $partId = $this->import->producer->unique_id . '_' . $row['artikelnummer'];

        $attributes = $this->getAttributesEn($row);

        if($row['artikelnummer']) {
            TvbPartsEn::updateOrCreate(
                ['part_id' => $partId],
                $attributes
            );
        }
    }


    public function getAttributesDe($row)
    {
        $attributes = [
            'producer_id'                   =>  'tvb',
            'producer_name'                 =>  'TVB GmbH',
            'part_number'                   =>  $row['artikelnummer'],
            'part_name'                     =>  $row['bezeichnung_de'],
            'country_of_origin'             =>  $this->getCountryOfOrigin($row),
            'stock_part_id'                 =>  $row['artikelnummer'],
            'reprocurement_time'            =>  5,
            'weight'                        =>  $this->getWeight($row),
            'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
            'preferential_beneficiary_eu'   =>  0,
            'stock'                         =>  json_encode([ "11" =>  0]),
            'part_specifications'           =>  $this->getPartSpecifications($row),
            'table_detail_image_link'       =>  $row['produktbilder'] !== '/' ? env('APP_URL').'/storage/producers/tvb/partImages/'.$row['produktbilder']: null,
            'image_links'                   =>  $row['produktbilder'] !== '/' ? env('APP_URL').'/storage/producers/tvb/partImages/'.$row['produktbilder']: null,
            'mapping_helper'                =>  $row['bezeichnung_de'],
        ];

        return $attributes;
    }

    public function getAttributesEn($row)
    {
        $attributes = [
            'producer_id'                   =>  'tvb',
            'producer_name'                 =>  'TVB GmbH',
            'part_number'                   =>  $row['artikelnummer'],
            'part_name'                     =>  $row['bezeichnung_en'],
            'country_of_origin'             =>  $this->getCountryOfOrigin($row),
            'stock_part_id'                 =>  $row['artikelnummer'],
            'reprocurement_time'            =>  5,
            'weight'                        =>  $this->getWeight($row),
            'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
            'preferential_beneficiary_eu'   =>  0,
            'stock'                         =>  json_encode([ "11" =>  0]),
            'part_specifications'           =>  $this->getPartSpecifications($row),
            'table_detail_image_link'       =>  $row['produktbilder'] !== '/' ? env('APP_URL').'/storage/producers/tvb/partImages/'.$row['produktbilder']: null,
            'image_links'                   =>  $row['produktbilder'] !== '/' ? env('APP_URL').'/storage/producers/tvb/partImages/'.$row['produktbilder']: null,
            'mapping_helper'                =>  $row['bezeichnung_de'],
        ];

        $attributes['part_name']            = str_replace('Sägeschränkmessuhr', 'Saw Setting Dial Gauge', $attributes['part_name'] );

        return $attributes;
    }

    public function getPartSpecifications($row)
    {
        $specifications = explode('I', $row['spezifikationen']);

        $partSpecifications = [];

        foreach ($specifications as $specification) {

            if(Str::of($specification)->trim()->isNotEmpty()) {
                $partSpecifications[trim(explode(':', $specification)[0])] = trim(explode(':', $specification)[1])." mm";
            }
        }

        return json_encode(array_filter($partSpecifications));
    }


    public function getCountryOfOrigin($row)
    {

        switch (trim($row['ursprungsland'])) {
            case 'Frankreich':
                return 'FR';
            case 'Japan':
                return 'JP';
            case 'Deutschland':
                return 'DE';
        }
    }

    public function getWeight($row)
    {
        $weight = Str::of($row['gewicht'])->remove('kg');

        $weight = Str::of($weight)->replace(',', '.');

        return floatval(trim($weight));
    }


    public function getCustomsTariffNumbers($row)
    {
        $tariffNumber = str_replace('.', '', $row['warentarifnummer']);
        $customSetting = $this->customsSettings->where('customs_tariff_number_ch', $tariffNumber)->first();

        return [
            'EU'    =>  $tariffNumber,
            'CH'    =>  $customSetting ? $customSetting->customs_tariff_number_ch : null,
        ];
    }


}
