<?php


namespace App\Producers\Nachreiner\ImportScripts;


use App\Models\CustomsSetting\CustomsSetting;
use App\Producers\Nachreiner\Models\NachreinerPartsDe;
use App\Producers\Nachreiner\Models\NachreinerPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class NachreinerBasicImport implements ToCollection, WithHeadingRow
{
    public $import;

    public $customsSettings;

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

    public function importPartsDe($row)
    {
        $partId = $this->import->producer->unique_id . '_' . $row['suchwort'];

        $attributes = $this->getAttributes($row);

        NachreinerPartsDe::updateOrCreate(
            ['part_id' => $partId],
            $attributes
        );
    }

    public function importPartsEn($row)
    {
        $partId = $this->import->producer->unique_id . '_' . $row['suchwort'];

        $attributes = $this->getAttributes($row);

        NachreinerPartsEn::updateOrCreate(
            ['part_id' => $partId],
            $attributes
        );
    }

    public function getAttributes($row) {
        $attributes = [
            'producer_id'                   =>  'nachreiner',
            'producer_name'                 =>  'Nachreiner',
            'part_number'                   =>  $row['suchwort'],
            'part_specifications'           =>   $this->getPartSpecifications($row),
            'stock_part_id'                 =>  $row['identnummer'],
            'reprocurement_time'            =>  21,
            'weight'                        =>  $row['gewicht'],
            'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
            'preferential_beneficiary_eu'   =>  $row['präferenzbegünstigt'] = "Ja" ? 1 : 0,
            'country_of_origin'             =>  'DE',
            'stock'                         =>  json_encode([ "1" =>  0]),
            'sort_value'                    =>  $row['durchmesser'],
        ];

        return $attributes;
    }

    public function getPartSpecifications($row)
    {
        return json_encode([
            'd1'    =>  number_format($row['durchmesser'] , 2)." mm",
            'd2'    =>  number_format($row['schaftdurchmesser'] , 2)." mm",
            'l1'    =>  number_format($row['gesamtlange'] , 2)." mm",
            'l2'    =>  $row['schneidelange']  > 0 ? number_format($row['schneidelange'] , 2)." mm" : null,
            'l3'    =>  $row['nutzlange']  > 0 ? number_format($row['nutzlange']  , 2)." mm": null
        ]);
    }

    public function getCustomsTariffNumbers($row)
    {
        $customSetting = $this->customsSettings->where('customs_tariff_number_eu', $row['warennummer'])->first();

        return [
            'EU'    => $row['warennummer'],
            'CH'    =>  $customSetting ? $customSetting->customs_tariff_number_ch : null
        ];
    }
}
