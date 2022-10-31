<?php


namespace App\Producers\Diametal\ImportScripts;

use App\Models\CustomsSetting\CustomsSetting;
use App\Producers\Diametal\Models\DiametalPartsDe;
use App\Producers\Diametal\Models\DiametalPartsEn;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class DiametalBasicImport implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{

    public $customsSettings, $images;

    public function basicImport()
    {
        $this->images = scandir(public_path('/storage/producers/diametal/partImages'));

        $this->customsSettings = CustomsSetting::all();

        $files = Storage::disk('localDiametalMasterData')->allFiles();

        foreach ($files as $file) {
            Excel::import($this, storage_path('/app/public/producers/diametal/masterData/'. $file));
        }
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => "|"
        ];
    }


    public function collection(Collection $rows)
    {
        $this->importParts($rows);
    }

    public function importParts($rows)
    {
        foreach ($rows as $row) {
            $this->importPartsDe($row);
        }

    }

    public function importPartsDe($row)
    {

        var_dump($row);
        $partId = 'diametal' . '_' . $row['artikelnr'];

        $attributes                 = $this->getAttributes($row);

        if($partId) {
            $attributes['part_name']    = $row['kurztext'].' - '.$row['text_de'];
            DiametalPartsDe::updateOrCreate(
                ['part_id' => $partId],
                $attributes
            );

            $attributes['part_name']    = $row['kurztext'].' - '.$row['text_us'];
            DiametalPartsEn::updateOrCreate(
                ['part_id' => $partId],
                $attributes
            );
        }
    }

    public function getAttributes($row)
    {
        $attributes = [
            'producer_id'                   =>  'diametal',
            'producer_name'                 =>  'Diametal',
            'part_number'                   =>  $row['artikelnr'],
            'country_of_origin'             =>  $row['ursprungsland'],
            'stock_part_id'                 =>  $row['artikelnr'],
            'weight'                        =>  floatval($row['gewicht']),
            'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
            'price'                         =>  $this->getPrice($row),
            'preferential_beneficiary_eu'   =>  $row['ursprungsland'] === 'Schweiz' ? 1 : 0,
            'stock'                         =>  json_encode([ 2 =>  0]),
            'reprocurement_time'            =>  21,
            'part_specifications'           =>  $this->getPartSpecifications($row),
            'thread_type'                   =>  $row['gewinde'] ? number_format(floatval($row['gewinde'])   , 2) : null,
            'pitch'                         =>  $row['steigung'] ? number_format(floatval($row['steigung'])   , 2)." mm" : null,
            'pitch_value'                   =>  $row['steigung'] ? number_format(floatval($row['steigung'])   , 2) : null,
            'table_detail_image_link'       =>  $this->getTableDetailImageLink($row),
            'image_links'                   =>   json_encode($this->getImageLinks($row))
        ];

        return $attributes;
    }

    public function getTableDetailImageLink($row)
    {
        $link = Arr::first($this->images, function ($value, $key) use ($row) {
            if(str_contains($value, $row['kurztext'] ) && str_contains($value, 'png')){
                return $value;
            }
        });

        if($link) {
            return env('APP_URL').'/storage/producers/diametal/partImages/'.$link;
        }
    }

    public function getImageLinks($row)
    {
        $imageLinks = [];
        foreach ($this->images as $image)
        {
            if(str_contains($image, $row['kurztext'] ) && str_contains($image, 'png')){
                array_push($imageLinks, env('APP_URL').'/storage/producers/diametal/partImages/'.$image);
            }

        }
        return $imageLinks;
    }

    public function getPartSpecifications($row)
    {
        $partSpecifications = [

            'D1'    =>  $row['d1'] ? number_format($row['d1'] , 2)." mm" : null,
            'D2'    =>  $row['d2'] ? number_format($row['d2'] , 2)." mm" : null,
            'L1'    =>  $row['l1'] ? number_format($row['l1'] , 2)." mm" : null,
            'L2'    =>  $row['l2'] ? number_format($row['l2']  , 2)." mm" : null,
            'R'    =>   $row['r'] ? number_format($row['r']  , 2)." mm" : null,
            'P'     =>  $row['steigung'] ? number_format(floatval($row['steigung'])   , 2)." mm" : null,
        ];

        return json_encode(array_filter($partSpecifications));
    }

    public function getCustomsTariffNumbers($row)
    {
        $tariffNumber = $row['statistische_warennr'];
        $customSetting = $this->customsSettings->where('customs_tariff_number_ch', $tariffNumber)->first();

        return [
            'CH'    =>  $tariffNumber,
            'EU'    =>  $customSetting ? $customSetting->customs_tariff_number_eu : null,
        ];
    }

    public function getPrice($row)
    {
        return json_encode(            [
            'currency'  =>  'CHF',
            'value'   => floatval($row['vp_chf'])
        ]);
    }
}
