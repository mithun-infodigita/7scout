<?php


namespace App\Producers\Dixi\ImportScripts;

use App\Producers\Dixi\Models\DixiPartsDe;
use App\Producers\Dixi\Models\DixiPartsEn;
use App\Producers\Dixi\Models\DixiPartsFr;
use App\Producers\Dixi\Models\DixiPartsIt;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class DixiPriceImport implements ToCollection, WithHeadingRow
{
    public $import;

    public function priceImport($import)
    {
        $this->import = $import;

        $file = $import->getMedia('priceFiles')->where('name', '7-industry')->first();

        Excel::import($this, $file->getPath());
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
            $part = DixiPartsDe::where('part_id', 'dixi_'.$row['goo_major_reference'])->first();
            if($part) {
                $part->update([
                    'price->CH->currency' => 'CHF',
                    'price->CH->value' => $row['tta_price'],
                    'price->LI->currency' => 'CHF',
                    'price->LI->value' => $row['tta_price'],
                    'price->DE->currency' => 'CHF',
                    'price->DE->value' => $row['tta_price']
                ]);
            }
        }
    }

    public function importEnglishPrices($rows)
    {
        foreach ($rows as $row) {

            $part = DixiPartsEn::where('part_id', 'dixi_'.$row['goo_major_reference'])->first();

            if($part) {
                $part->update([
                    'price->CH->currency' => 'CHF',
                    'price->CH->value' => $row['tta_price'],
                    'price->LI->currency' => 'CHF',
                    'price->LI->value' => $row['tta_price'],
                    'price->DE->currency' => 'CHF',
                    'price->DE->value' => $row['tta_price']
                ]);
            }
        }
    }

    public function importFrenchPrices($rows)
    {
        foreach ($rows as $row) {

            $part = DixiPartsFr::where('part_id', 'dixi_'.$row['goo_major_reference'])->first();

            if($part) {
                $part->update([
                    'price->CH->currency' => 'CHF',
                    'price->CH->value' => $row['tta_price'],
                    'price->LI->currency' => 'CHF',
                    'price->LI->value' => $row['tta_price'],
                    'price->DE->currency' => 'CHF',
                    'price->DE->value' => $row['tta_price']
                ]);
            }
        }
    }

    public function importItalianPrices($rows)
    {
        foreach ($rows as $row) {

            $part = DixiPartsIt::where('part_id', 'dixi_'.$row['goo_major_reference'])->first();

            if($part) {
                $part->update([
                    'price->CH->currency' => 'CHF',
                    'price->CH->value' => $row['tta_price'],
                    'price->LI->currency' => 'CHF',
                    'price->LI->value' => $row['tta_price'],
                    'price->DE->currency' => 'CHF',
                    'price->DE->value' => $row['tta_price']
                ]);
            }
        }
    }
}
