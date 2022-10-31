<?php


namespace App\Producers\Gloor\ImportScripts;

use App\Producers\Gloor\Models\GloorPartsDe;
use App\Producers\Gloor\Models\GloorPartsEn;
use App\Producers\Gloor\Models\GloorPartsFr;
use App\Producers\Gloor\Models\GloorPartsIt;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class GloorPriceImport implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
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
            case 'fr':
                $this->importFrenchPrices($rows);
                break;
            case 'it':
                $this->importItalianPrices($rows);
                break;
        }
    }

    public function importGermanPrices($rows)
    {
        foreach ($rows as $row) {

            $part = GloorPartsDe::where('part_id', 'gloor_'.$row['n'])->first();

            $this->updatePrice($part, $row);
        }
    }

    public function importEnglishPrices($rows)
    {
        foreach ($rows as $row) {

            $part = GloorPartsEn::where('part_id', 'gloor_'.$row['n'])->first();

            $this->updatePrice($part, $row);
        }
    }

    public function importFrenchPrices($rows)
    {
        foreach ($rows as $row) {

            $part = GloorPartsFr::where('part_id', 'gloor_'.$row['n'])->first();

            $this->updatePrice($part, $row);
        }
    }

    public function importItalianPrices($rows)
    {
        foreach ($rows as $row) {

            $part = GloorPartsIt::where('part_id', 'gloor_'.$row['n'])->first();

            $this->updatePrice($part, $row);
        }
    }

    public function updatePrice($part, $row)
    {
        if($part) {

            $part->update([
                'price'   =>  json_encode([
                    'currency'      =>  'CHF',
                    'value'         =>  $row['chf']

                ]),
            ]);
        }
    }


}
