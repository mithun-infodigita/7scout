<?php


namespace App\Imports\StandardImport\ImportScripts;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use App\Models\Column\Column;
use Arr;

class StandardBasicImport implements ToCollection, WithHeadingRow
{
    public $import;

    public $datatable;

    public $columns;

    public function basicImport($import)
    {
        $this->import = $import;

        $this->getColumns();

        $this->setDataTable();

        $this->truncateTable();

        $files = $import->getMedia();

        foreach ($files as $file) {
            if (file_exists($file->getPath())) {
                Excel::import($this, $file->getPath());
            }
        }
    }

    public function setDataTable()
    {
        $this->datatable = $this->import->producer->unique_id."_parts_".$this->import->language;
    }

    public function getColumns()
    {
        $this->columns = Column::where('id', '<>', null)->get()->pluck('name')->toArray();
    }

    public function truncateTable()
    {
        DB::table($this->datatable)->truncate();
    }

    public function collection(Collection $rows)
    {
        $this->importParts($rows);
    }

    public function importParts($rows)
    {

        foreach ($rows as $row) {

            $row = $row->toArray();

            $inserts = [];

            foreach ($this->columns as $key => $name) {

                if(array_key_exists($name, $row)) {

                    switch ($name) {

                        case 'part_id' : $inserts[$name] = $this->import->producer->unique_id.'_'.$row['part_number'];
                            break;

                        case 'producer_id' : $inserts[$name] = $this->import->producer->unique_id;
                            break;

                        case 'customs_tariff_numbers' : $inserts[$name] = $this->getCustomsTariffNumbers($row[$name]);
                            break;

                        case 'part_specifications' : $inserts[$name] = $this->getPartSpecifications($row[$name]);
                            break;

                        case 'table_detail_image_link' : $inserts[$name] = $this->getTableDetailImageLink($row[$name]);
                            break;

                        case 'image_links' : $inserts[$name] = $this->getImageLinks($row[$name]);
                            break;

                        case 'document_links' : $inserts[$name] = $this->getDocumentLinks($row[$name]);
                            break;

                        case 'price' : $inserts[$name] = $this->getPrice($row[$name]);
                            break;

                        case 'stock' : $inserts[$name] = $this->getStock($row[$name]);
                            break;

                        case 'inline_pdf_url' : $inserts[$name] = $this->getInlinePdfUrl($row[$name]);
                            break;

                        default: $inserts[$name] = $row[$name];
                    }
                }
            }

            DB::table($this->datatable)->insert($inserts);

        }
    }

    public function getCustomsTariffNumbers($numbers)
    {
        if($numbers) {

            $customTariffNumbers = [];

            foreach(explode(';;', $numbers) as $number) {

                $keyAndNumber = explode('::', $number);

                $customTariffNumbers[$keyAndNumber[0]] = $keyAndNumber[1];
            }
            return json_encode($customTariffNumbers);
        }
        return null;
    }

    public function getPartSpecifications($specifications)
    {
        if($specifications) {

            $partSpecifications = [];

            foreach(explode(';;', $specifications) as $specification) {

                $keyValues = explode('::', $specification);

                $partSpecifications[$keyValues[0]] = $keyValues[1];
            }
            return json_encode($partSpecifications);
        }
        return json_encode([]);
    }


    public function getTableDetailImageLink($filename)
    {
        if($filename) {
            return env('APP_URL').'/storage/producers/'.$this->import->producer->unique_id.'/partImages/'.$filename;
        }
        return null;
    }

    public function getImageLinks($filenames)
    {
        if($filenames) {

            $imageLinks = [];

            foreach(explode(';;', $filenames) as $filename) {

                $imageLinks[] = env('APP_URL') . '/storage/producers/' . $this->import->producer->unique_id . '/partImages/' . $filename;
            }
            return json_encode(Arr::flatten($imageLinks));
        }
        return json_encode([]);
    }

    public function getDocumentLinks($documents)
    {
        if($documents) {

            $documentLinks = [];

            foreach(explode(';;', $documents) as $document) {
                $linkName = explode(':', $document) ;

                    $documentLinks[] = [
                        'link' => env('APP_URL') . '/storage/producers/' . $this->import->producer->unique_id . '/pdfs/' . $linkName[0],
                        'file_name' => $linkName[1]
                    ];
            }
            return json_encode($documentLinks);
        }
        return json_encode([]);
    }

    public function getInlinePdfUrl($filename)
    {
        if($filename) {
            return env('APP_URL').'/storage/producers/'.$this->import->producer->unique_id.'/pdfs/'.$filename;
        }
        return null;
    }

    public function getPrice($prices)
    {
        if($prices) {

            $currencyWithPrice = [];

            foreach(explode(';;', $prices) as $price) {

                $keyValues = explode('::', $price);

                $currencyWithPrice[$keyValues[0]] = $keyValues[1];
            }
            return json_encode($currencyWithPrice);
        }
        return json_encode([]);
    }

    public function getStock($stocks)
    {
        if($stocks) {

            $stockValues = [];

            foreach(explode(';;', $stocks) as $stock) {

                $keyValues = explode('::', $stocks);

                $stockValues[$keyValues[0]] = $keyValues[1];
            }
            return json_encode($stockValues);
        }
        return json_encode([]);
    }
}
