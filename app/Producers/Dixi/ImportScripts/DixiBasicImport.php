<?php
namespace App\Producers\Dixi\ImportScripts;

use App\Models\CustomsSetting\CustomsSetting;
use App\Producers\Dixi\Models\DixiPartsDe;
use App\Producers\Dixi\Models\DixiPartsEn;
use App\Producers\Dixi\Models\DixiPartsFr;
use App\Producers\Dixi\Models\DixiPartsIt;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class DixiBasicImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    public $import;

    public $dixiMiddleware;

    public $images;

    public $customsSettings;

    public function basicImport($import)
    {
        $this->import = $import;

        $this->customsSettings = CustomsSetting::all();

        $this->images = scandir(public_path('/storage/producers/dixi/partImages'));

        $dixiMiddlewareImport = new DixiMiddlewareImport();
        $this->dixiMiddleware = $dixiMiddlewareImport->basicImport($import);

        $files = $import->getMedia();

        foreach ($files as $file) {
            if (file_exists($file->getPath())) {
                Excel::import($this, $file->getPath());
            }
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            $row = $row->toArray();

            $item = [];
            $item['L'] = null;
            $item['L1'] = null;
            $item['L2'] = null;
            $item['L3'] = null;
            $item['D'] = null;
            $item['D1'] = null;
            $item['D2'] = null;
            $item['D3'] = null;
            $item['Z'] = null;
            $item['D nom.'] = null;
            $item['R'] = null;
            $item['Tolérance'] = null;
            $item['Pas'] = null;

            $materialGroup = explode('-', $row['gco_good_category_wording']);

            $item['part_name_german']   = $row['allemand'];
            $item['part_name_english']   = $row['anglais'];
            $item['part_name_french']   = $row['francais'];
            $item['part_name_italian']   = $row['italien'];

            $item['material_group'] = preg_replace('/[^0-9]/', '', $materialGroup[0]);


            $item['table_detail_image_link'] = Arr::first($this->images, function ($value, $key) use ($item) {
                if(str_contains($value, $item['material_group'] )){
                    return $value;
                }
            });

            if(array_key_exists(1, $materialGroup)) {
                $item['part_name'] = trim($materialGroup[1]);


                if (array_key_exists($item['material_group'], $this->dixiMiddleware)) {
                    foreach ($this->dixiMiddleware[$item['material_group']] as $key => $value) {
                        if (array_key_exists($key, $row)) {
                            $item[$value] = $row[$key];
                        }
                    }

                }
                elseif (array_key_exists($item['material_group']."1", $this->dixiMiddleware)) {
                    foreach ($this->dixiMiddleware[$item['material_group']."1"] as $key => $value) {
                        if (array_key_exists($key, $row)) {
                            $item[$value] = $row[$key];
                        }
                    }
                }
                elseif (array_key_exists(substr($item['material_group'],1), $this->dixiMiddleware)) {
                    foreach ($this->dixiMiddleware[substr($item['material_group'],1)] as $key => $value) {
                        if (array_key_exists($key, $row)) {
                            $item[$value] = $row[$key];
                        }
                    }
                }
            }
            $this->importParts($row, $item);
        }
    }

    public function importParts($row, $item)
    {
        switch ($this->import->language) {
            case 'de':
                $this->importPartsDe($row, $item);
                break;
            case 'en':
                $this->importPartsEn($row, $item);
                break;
            case 'fr':
                $this->importPartsFr($row, $item);
                break;
            case 'it':
                $this->importPartsIt($row, $item);
                break;
        }
    }

    public function importPartsDe($row, $item)
    {
        $attributes = $this->getAttributes($row, $item);


        $attributes['part_name'] = $row['allemand'];

        DixiPartsDe::updateOrCreate([
            'part_id'   =>   'dixi_'.$row['goo_major_reference'],
        ],
            $attributes
        );
    }

    public function importPartsEn($row, $item)
    {
        $attributes = $this->getAttributes($row, $item);

        $attributes['part_name'] = $row['anglais'];

        DixiPartsEn::updateOrCreate([
            'part_id'   =>   'dixi_'.$row['goo_major_reference'],
        ],
            $attributes
        );
    }

    public function importPartsFr($row, $item)
    {
        $attributes = $this->getAttributes($row, $item);

        $attributes['part_name'] = $row['francais'];

        DixiPartsFr::updateOrCreate([
            'part_id'   =>   'dixi_'.$row['goo_major_reference'],
        ],
            $attributes
        );
    }

    public function importPartsIt($row, $item)
    {
        $attributes = $this->getAttributes($row, $item);

        $attributes['part_name'] = $row['italien'];

        DixiPartsIt::updateOrCreate([
            'part_id'   =>   'dixi_'.$row['goo_major_reference'],
        ],
            $attributes
        );
    }

    public function getAttributes($row, $item) {

        $attributes = [
            'producer_id'                   =>  'dixi',
            'producer_name'                 =>  'Dixi',
            'part_name'                     =>  $row['allemand'],
            'part_number'                   =>  $row['goo_major_reference'],
            'discount_group'                =>  $item['material_group'],
            'material_group'                =>  $item['material_group'],
            'table_detail_image_link'       =>  env('APP_URL').'/storage/producers/dixi/partImages/'.$item['table_detail_image_link'],
            'part_specifications'           =>   $this->getPartSpecifications($item),
            'part_specification_values'     =>  $this->getPartSpecificationValues($item),
            'reprocurement_time'            =>  21,
            'sort_value'                    =>  $item['D1'] ? number_format($item['D1'] , 3): 0,
            'weight'                        =>  $row['mea_net_weight'] / 1000,
            'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
            'preferential_beneficiary_eu'   =>  1,
            'country_of_origin'             =>  $row['origin'] === 'Switzerland' ? 'CH': null,
            'stock'                         =>  json_encode([
                '5' => 0
            ]),
        ];

        return $attributes;
    }

    public function getPartSpecifications($item)
    {
        $partSpecifications = [
            'D'     =>  $item['D'] ? number_format($item['D'] , 3)." mm" : null,
            'D1'    =>  $item['D1'] ? number_format($item['D1'] , 3)." mm" : null,
            'D2'    =>  $item['D2'] ? number_format($item['D2'] , 2)." mm" : null,
            'D3'    =>  $item['D3'] ? number_format($item['D3'] , 2)." mm" : null,
            'L'     =>  $item['L'] ? number_format($item['L'] , 2)." mm" : null,
            'L1'    =>  $item['L1'] ? number_format($item['L1'] , 2)." mm" : null,
            'L2'    =>  $item['L2'] ? number_format($item['L2']  , 2)." mm" : null,
            'Z'    =>   $item['Z'] ? number_format($item['Z']  , 0) : null,
            'D nom'  => $item['D nom.'],
            'R'         =>     $item['R'] ? number_format($item['R']  , 2)." mm" : null,
            'T nom'   =>     $item['Tolérance'] ? $item['Tolérance'] : null,
            'P'         =>     $item['Pas'] ? number_format(floatval($item['Pas'])   , 2)." mm" : null,
        ];

        $partSpecificationsFiltered = array_filter($partSpecifications, function ($item) {
            return $item != null;
        });

        return json_encode($partSpecificationsFiltered);
    }

    public function getPartSpecificationValues($item)
    {
        $partSpecificationValues = [
            'D'     =>  $item['D'] ? number_format($item['D'] , 3) : null,
            'D1'    =>  $item['D1'] ? number_format($item['D1'] , 3) : null,
            'D2'    =>  $item['D2'] ? number_format($item['D2'] , 2) : null,
            'D3'    =>  $item['D3'] ? number_format($item['D3'] , 2) : null,
            'L'     =>  $item['L'] ? number_format($item['L'] , 2) : null,
            'L1'    =>  $item['L1'] ? number_format($item['L1'] , 2) : null,
            'L2'    =>  $item['L2'] ? number_format($item['L2']  , 2) : null,
            'Z'     =>  $item['Z'] ? number_format($item['Z']  , 0) : null,
            'D nom'  =>  $item['D nom.'],
            'R'    =>  $item['R'] ? number_format($item['R']  , 2) : null,
            'T nom'   =>     $item['Tolérance'] ? $item['Tolérance'] : null,
            'P'    =>  $item['Pas'] ? number_format(floatval($item['Pas'])   , 2) : null,
        ];

        $partSpecificationValuesFiltered = array_filter($partSpecificationValues, function ($item) {
            return $item != null;
        });

        return json_encode($partSpecificationValuesFiltered);
    }

    public function getCustomsTariffNumbers($row)
    {
        $customSetting = $this->customsSettings->where('customs_tariff_number_ch', $row['code_tarifaire'])->first();

        return [
            'CH'    => $row['code_tarifaire'],
            'EU'    =>  $customSetting ? $customSetting->customs_tariff_number_eu : null
        ];
    }
}
