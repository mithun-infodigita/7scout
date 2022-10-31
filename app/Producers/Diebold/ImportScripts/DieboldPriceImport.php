<?php

namespace App\Producers\Diebold\ImportScripts;

use App\Producers\Diebold\Models\DieboldPartsDe;
use App\Producers\Diebold\Models\DieboldPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class DieboldPriceImport implements ToCollection, WithHeadingRow
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
            case 'en':
                $this->importEnglishPrices($rows);
                break;
        }
    }

    public function importGermanPrices($rows)
    {
        foreach ($rows as $row) {

            $part = DieboldPartsDe::where('part_number', (string)$row['teil'])->first();

           // $value = str_replace(".","",$row['preis'] );

            if($part) {
                $part->update([
                    'price'   =>  json_encode([
                        'currency'      =>  'EUR',
                        'value'         =>  $row['preis']
                    ]),
                ]);
            }

        }
    }

    public function importEnglishPrices($rows)
    {
        foreach ($rows as $row) {

            $part = DieboldPartsEn::where('part_number', (string)$row['teil'])->first();
           // $value = str_replace(".","",$row['preis'] );

            if($part) {
                $part->update([
                    'price'   =>  json_encode([
                        'currency'      =>  'EUR',
                        'value'         =>  $row['preis']
                    ]),
                ]);
            }

        }
    }
}
