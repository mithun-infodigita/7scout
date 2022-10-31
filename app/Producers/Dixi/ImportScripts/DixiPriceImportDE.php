<?php


namespace App\Producers\Dixi\ImportScripts;

use App\Producers\Dixi\Models\DixiPartsDe;
use App\Producers\Dixi\Models\DixiPartsFr;
use App\Producers\Dixi\Models\DixiPartsIt;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class DixiPriceImportDE implements ToCollection, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public $import;

    public function priceImport($import)
    {
        $this->import = $import;

        $file = $import->getMedia('priceFiles')->where('name', 'Dixi Preise DE')->first();

        Excel::import($this, $file->getPath());
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

            $part = DixiPartsDe::where('part_id', 'dixi_'.$row['ref_dixi'])->first();

            if($part) {
                $part->update([
                    'price->DE->currency' => 'EUR',
                    'price->DE->value' => $row['prix_vente_eur']
                ]);
            }
        }
    }

    public function importEnglishPrices($rows)
    {
        foreach ($rows as $row) {

            $part = DixiPartsDe::where('part_id', 'dixi_'.$row['ref_dixi'])->first();

            if($part) {
                $part->update([
                    'price->DE->currency' => 'EUR',
                    'price->DE->value' => $row['prix_vente_eur']
                ]);
            }
        }
    }

    public function importFrenchPrices($rows)
    {
        foreach ($rows as $row) {

            $part = DixiPartsFr::where('part_id', 'dixi_'.$row['ref_dixi'])->first();

            if($part) {
                $part->update([
                    'price->DE->currency' => 'EUR',
                    'price->DE->value' => $row['prix_vente_eur']
                ]);
            }
        }
    }

    public function importItalianPrices($rows)
    {
        foreach ($rows as $row) {

            $part = DixiPartsIt::where('part_id', 'dixi_'.$row['ref_dixi'])->first();

            if($part) {
                $part->update([
                    'price->DE->currency' => 'EUR',
                    'price->DE->value' => $row['prix_vente_eur']
                ]);
            }
        }
    }

}
