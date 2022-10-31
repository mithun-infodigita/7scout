<?php

namespace App\Producers\Nachreiner\ImportScripts;

use App\Producers\Nachreiner\Models\NachreinerPartsDe;
use App\Producers\Nachreiner\Models\NachreinerPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class NachreinerPriceImport implements ToCollection, WithHeadingRow
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

    public function importPrices($rows) {
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
            $part = NachreinerPartsDe::where('part_id', 'nachreiner_'.$row['suchwort'])->first();

            if($part) {
                $part->update([
                    'price' => json_encode([
                        'currency' => 'EUR',
                        'value' => $row['preis']

                    ]),
                ]);
            }
        }
    }

    public function importEnglishPrices($rows)
    {
        foreach ($rows as $row) {
            $part = NachreinerPartsEn::where('part_id', 'nachreiner_'.$row['suchwort'])->first();

            if($part) {
                $part->update([
                    'price' => json_encode([
                        'currency' => 'EUR',
                        'value' => $row['preis']
                    ]),
                ]);
            }
        }
    }

}
