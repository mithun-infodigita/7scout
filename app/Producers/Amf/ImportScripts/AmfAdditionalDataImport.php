<?php

namespace App\Producers\Amf\ImportScripts;

use App\Models\CustomsSetting\CustomsSetting;
use App\Producers\Amf\Models\AmfPartsDe;
use App\Producers\Amf\Models\AmfPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class AmfAdditionalDataImport implements ToCollection, WithHeadingRow
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

            $part = AmfPartsDe::where('part_id', 'amf_'.(string)$row['artikel'])->first();

            if($part) {
                $part->update([
                    'weight'                        =>  $row['gewicht'],
                    'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
                    'preferential_beneficiary_eu'   =>  $row['ursprungsland'] = "EU" ? 1: 0,
                    'country_of_origin'             =>  $row['ursprungsland'],
                    'discount_group'                =>  $row['rabattgruppe']
                ]);
            }

        }
    }

    public function importEnglishData($rows)
    {
        foreach ($rows as $row) {

            $part = AmfPartsEn::where('part_number', (string)$row['teil'])->first();

            if($part) {
                $part->update([
                    'weight'                        =>  $row['gewicht'],
                    'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
                    'preferential_beneficiary_eu'   =>  $row['ursprland.'] = "EU" ? 1: 0,
                    'country_of_origin'             =>  $row['ursprland'],
                    'discount_group'                =>  $row['rabattgruppe']
                ]);
            }

        }
    }

    public function getCustomsTariffNumbers($part)
    {
        $customSetting = $this->customsSettings->where('customs_tariff_number_eu', $part['zolltarif_nr'])->first();

        return [
            'EU'    => $part['zolltarif_nr'],
            'CH'    =>  $customSetting ? $customSetting->customs_tariff_number_ch : null
        ];
    }
}
