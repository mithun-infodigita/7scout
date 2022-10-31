<?php


namespace App\Producers\Parotec\ImportScripts;

use App\Models\CustomsSetting\CustomsSetting;
use App\Producers\Parotec\Models\ParotecPartsDe;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ParotecBasicImport implements ToCollection, WithHeadingRow
{
    public $import;

    public $customsSettings;

    public $images;

    public $toothForm;

    public function basicImport($import)
    {
        $this->customsSettings = CustomsSetting::all();

        $this->import = $import;

        $this->images = scandir(public_path('/storage/producers/parotec/partImages'));

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
            }
        }
    }


    public function importPartsDe($row)
    {
        $row = $row->toArray();

        $partId = $this->import->producer->unique_id . '_' . $row['artikelnr'];

        $attributes = $this->getAttributes($row);

        $attributes['part_name' ] = $row['bezeichnung'];

        ParotecPartsDe::updateOrCreate(
            ['part_id' => $partId],
            $attributes
        );
    }


    public function getAttributes($row)
    {
        $attributes = [
            'producer_id' => 'parotec',
            'producer_name' => 'Parotec AG',
            'part_number' => $row['artikelnr'],
            'part_specifications' => $this->getPartSpecificationsDe($row),
            'mapping_helper' => $this->getMappingHelper($row),
            'reprocurement_time' => 35,
            'weight' => floatval($row['gewicht_in_kg']),
            'customs_tariff_numbers' => json_encode($this->getCustomsTariffNumbers($row)),
            'preferential_beneficiary_eu' => 0,
            'country_of_origin' => 'CH',
            'stock' => json_encode(["10" => 0]),
            'price' => $this->getPrice($row),
            'table_detail_image_link' => $this->getTableDetailImageLink($row),
            'image_links' => json_encode($this->getImageLinks($row)),
            'inline_pdf_url_pages' => $row['seite'],
        ];

        return $attributes;
    }

    public function getPartSpecificationsDe($row)
    {

        $partSpecifications = [
            'Stichmass'         =>  array_key_exists('stichmass', $row) && is_numeric($row['stichmass'])? number_format($row['stichmass'] , 0)." mm" : null,
            'Länge'            =>  array_key_exists('lange', $row) && is_numeric($row['lange'])? number_format($row['lange'] , 0)." mm" : null,
            'Durchmesser'       =>  array_key_exists('lange', $row) && str_contains($row['lange'], 'ø' )? str_replace('ø', '', $row['lange'] )." mm" : null,
            'Breite'            =>  array_key_exists('breite', $row) && is_numeric($row['breite'])? number_format($row['breite'] , 0)." mm" : null,
            'Höhe'              =>  array_key_exists('hohe', $row) && is_numeric($row['hohe'])? number_format($row['hohe'] , 0)." mm" : null,
            'Gewicht'           =>  array_key_exists('gewicht_in_kg', $row) && is_numeric($row['gewicht_in_kg'])? number_format($row['gewicht_in_kg'] , 3)." kg" : null,
        ];

        $partSpecificationsFiltered = array_filter($partSpecifications, function ($item) {
            return $item != null;
        });

        return json_encode($partSpecificationsFiltered);
    }

    public function getPrice($row)
    {
        if($row['preis_in_eur'] !== 'auf Anfrage') {
            return json_encode([
                'currency' => 'EUR',
                'value' => $row['preis_in_eur']

            ]);
        }

        return null;
    }
    public function getMappingHelper($row)
    {
        return $row['artikelnr'];
    }



    public function getCustomsTariffNumbers($row)
    {
        $tariffNumber = str_replace('.', '', $row['zolltarifnr']);
        $customSetting = $this->customsSettings->where('customs_tariff_number_eu', $tariffNumber)->first();

        return [
            'EU'    =>  $customSetting ? $customSetting->customs_tariff_number_eu : null,
            'CH'    =>  $tariffNumber
        ];
    }


    public function getTableDetailImageLink($row)
    {

        $link = Arr::first($this->images, function ($value, $key) use ($row) {
            if(str_contains($value, substr($row['bilder'],0,6) )){
                return $value;
            }
        });

        if($link) {
            return env('APP_URL').'/storage/producers/parotec/partImages/'.$link;
        }
    }

    public function getImageLinks($row)
    {
        $imageLinks = [];

        foreach ($this->images as $image) {
            if(str_contains($image, substr($row['bilder'],0,8) )){
                array_push($imageLinks, env('APP_URL').'/storage/producers/parotec/partImages/'.$image);
            }
        }

        return $imageLinks;
    }
}
