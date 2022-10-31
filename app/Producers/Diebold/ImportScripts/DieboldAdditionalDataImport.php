<?php

namespace App\Producers\Diebold\ImportScripts;

use App\Models\CustomsSetting\CustomsSetting;
use App\Producers\Diebold\Models\DieboldPartsDe;
use App\Producers\Diebold\Models\DieboldPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class DieboldAdditionalDataImport implements ToCollection, WithHeadingRow
{
    public $import;

    public $customsSettings;

    public function additionalDataImport($import)
    {
        $this->import = $import;

        $this->customsSettings = CustomsSetting::all();

        $files = $import->getMedia('additionalFiles');

        foreach ($files as $file) {
            if (file_exists($file->getPath())) {
                Excel::import($this, $file->getPath());
            }
        }
    }

    public function collection(Collection $rows)
    {
        $this->importAdditionalData($rows);
    }

    public function importAdditionalData($rows)
    {
        switch ($this->import->language) {
            case 'de':
                $this->importGermanData($rows);
                break;
            case 'en':
                $this->importEnglishData($rows);
                break;
        }
    }

    public function importGermanData($rows)
    {
        foreach ($rows as $row) {

            $part = DieboldPartsDe::where('part_number', (string)$row['teil'])->first();

            if($part) {
                $part->update([
                    'weight'                        =>  str_replace(",",".",$row['gewicht'] ),
                    'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
                    'preferential_beneficiary_eu'   =>  $row['präf.'] = "ja" ? 1: 0,
                    'country_of_origin'             =>  $row['urspr'],
                    'discount_group'                =>  $row['rabgr'],
                    'video_url'                     =>  $row['video_url']
                ]);
            }

        }
    }

    public function importEnglishData($rows)
    {
        foreach ($rows as $row) {

            $part = DieboldPartsEn::where('part_number', (string)$row['teil'])->first();

            if($part) {
                $part->update([
                    'weight'                        =>  str_replace(",",".",$row['gewicht'] ),
                    'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
                    'preferential_beneficiary_eu'   =>  $row['präf.'] = "ja" ? 1: 0,
                    'country_of_origin'             =>  $row['urspr'],
                    'discount_group'                =>  $row['rabgr'],
                    'video_url'                     =>  $row['video_url']
                ]);
            }

        }
    }

    public function getCustomsTariffNumbers($part)
    {
        $customSetting = $this->customsSettings->where('customs_tariff_number_eu', $part['zolltarifnr'])->first();

        return [
            'EU'    => $part['zolltarifnr'],
            'CH'    =>  $customSetting ? $customSetting->customs_tariff_number_ch : null
        ];
    }
}
