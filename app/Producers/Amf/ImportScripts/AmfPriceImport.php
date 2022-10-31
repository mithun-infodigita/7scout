<?php

namespace App\Producers\Amf\ImportScripts;

use App\Producers\Amf\Models\AmfPartsDe;
use App\Producers\Amf\Models\AmfPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class AmfPriceImport implements ToCollection, WithHeadingRow
{
    public $import;

    public function priceImport($import)
    {
        $this->import = $import;

        $files = $import->getMedia('priceFiles');

        foreach ($files as $file) {
            if (file_exists($file->getPath())) {
                Excel::import($this, $file->getPath());
            }
        }
    }

    public function collection(Collection $rows)
    {
        $this->importPrices($rows);
    }

    public function importPrices($rows)
    {
        switch ($this->import->language) {
            case 'de':
                $this->importGermanPrices($rows);
                break;

        }
    }

    public function importGermanPrices($rows)
    {
        foreach ($rows as $row) {

            $part = AmfPartsDe::where('part_id', 'amf_'.(string)$row['artikel'])->first();


            if($part) {
                $part->update([
                    'price'   =>  json_encode([
                        'currency'      =>  'EUR',
                        'value'         =>  floatval($row['preis_1']) * 1.084
                    ]),
                ]);
            }

        }
    }


}
