<?php


namespace App\Producers\Botek\ImportScripts;


use App\Producers\Botek\Models\BotekPartsDe;
use App\Producers\Botek\Models\BotekPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class BotekPriceImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public $import;

    public function priceImport($import)
    {
        $this->import = $import;

        $files = $import->getMedia('priceFiles');

        foreach ($files as $file) {

            Excel::import($this, $file->getPath());
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
        $this->importPrices($rows);
    }

    public function importPrices($rows)
    {
        switch($this->import->language) {
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

            $part = BotekPartsDe::where('part_id', 'botek_'.$row['artikel'])->first();

            $this->updatePrice($part, $row);
        }
    }

    public function importEnglishPrices($rows)
    {
        foreach ($rows as $row) {

            $part = BotekPartsEn::where('part_id', 'botek_'.$row['artikel'])->first();

            $this->updatePrice($part, $row);
        }
    }


    public function updatePrice($part, $row)
    {
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
