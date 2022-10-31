<?php


namespace App\Producers\Gloor\ImportScripts;

use App\Models\CustomsSetting\CustomsSetting;
use App\Producers\Gloor\Models\GloorPartsDe;
use App\Producers\Gloor\Models\GloorPartsFr;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class GloorBasicImport implements ToCollection, WithHeadingRow
{
    public $import;

    public $customsSettings;

    public $images;

    public $toothForm;

    public function basicImport($import)
    {

        $this->customsSettings = CustomsSetting::all();

        $this->import = $import;

        $this->images = scandir(public_path('/storage/producers/gloor/partImages'));

        $files = $import->getMedia();

        foreach ($files as $file) {
            if (file_exists($file->getPath())) {


                $headingRow = (new HeadingRowImport)->toArray($file->getPath());

                foreach ($headingRow[0][0] as $column) {
                    if($column === 'zahnform_a') {
                        $this->toothForm = 'A';
                    }
                    if($column === 'zahnform_b') {
                        $this->toothForm = 'B';
                    }
                }


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
                case 'fr':
                    $this->importPartsFr($row);
                    break;
            }
        }
    }


    public function importPartsDe($row)
    {
        $row = $row->toArray();

        $partId = $this->import->producer->unique_id . '_' . $row['n'];

        $attributes = $this->getAttributes($row);

        $attributes['part_name' ] = $row['titel_de'];

        GloorPartsDe::updateOrCreate(
            ['part_id' => $partId],
            $attributes
        );
    }

    public function importPartsFr($row)
    {
        $partId = $this->import->producer->unique_id . '_' . $row['n'];

        $attributes = $this->getAttributes($row);

        $attributes['part_name' ] = $row['titel_fr'];

        GloorPartsFr::updateOrCreate(
            ['part_id' => $partId],
            $attributes
        );
    }

    public function getAttributes($row)
    {
        $materialGroup = explode('.',$row['n'])[0];

        $attributes = [
            'producer_id'                   =>  'gloor',
            'producer_name'                 =>  'Gloor',
            'part_number'                   =>  $row['n'],
            'part_specifications'           =>   $this->getPartSpecifications($row),
            'part_specification_values'     =>  $this->getPartSpecificationValues($row),
            'material_group'                =>  $materialGroup,
            'stock_part_id'                 =>  $row['n'],
            'reprocurement_time'            =>  21,
            'weight'                        =>  floatval(str_replace('kg', '', $row['gewicht_stk'])),
            'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
            'preferential_beneficiary_eu'   =>  $row['praferenzieller_ursprung'] = "Ja" ? 1 : 0,
            'country_of_origin'             =>  $row['ursprungsland'],
            'stock'                         =>  json_encode([ "6" =>  $row['lagerbestand']]),
            'sort_value'                    =>  $this->getSortValue($row),
            'part_description'              =>  trim(str_replace('• ', '', $row['beschreibung'])),
            'table_detail_image_link'       =>  $this->getImageAndDrawingLinks($row, $materialGroup)['table_detail_image_link'] ? env('APP_URL').'/storage/producers/gloor/partImages/'.$this->getImageAndDrawingLinks($row, $materialGroup)['table_detail_image_link'] : null,
            'image_links'                   =>  $this->getImageAndDrawingLinks($row, $materialGroup)['image_links'] ? env('APP_URL').'/storage/producers/gloor/partImages/'.$this->getImageAndDrawingLinks($row, $materialGroup)['image_links'] : null,
            'tooth_form'                    =>  $this->toothForm,
            'coating'                       =>  array_key_exists('beschichtung', $row) ? $row['beschichtung'] : null,
            'direction_of_rotation'         =>  array_key_exists('drehrichtung', $row) ? $row['drehrichtung'] : null,
            'thread'                        =>  array_key_exists('iso', $row) ? $row['iso'] : null,
        ];

        return $attributes;
    }

    public function getPartSpecifications($row)
    {

        $partSpecifications = [
            'm'     =>  array_key_exists('m', $row) ? 'M'.' '.$row['m'] : null,
            's'    =>  array_key_exists('s', $row) ? number_format($row['s'] , 2)." mm" : null,
            'p'    =>  array_key_exists('p', $row) ? number_format($row['p'] , 3)." mm" :null,
            't'    =>  array_key_exists('t', $row) ? number_format($row['t'] , 3)." mm" :null,
            'd1'    =>  array_key_exists('d1', $row) ? number_format($row['d1'] , 2)." mm" : null,
            'd2'    =>  $this->getLcd2($row) ? number_format($this->getLcd2($row) , 2)." mm" : null,
            'd3'    =>  array_key_exists('d3', $row) ? number_format($row['d3'] , 2)." mm" : null,
            'l1'    =>  array_key_exists('l1', $row) ? number_format($row['l1'] , 2)." mm" : null,
            'l2'    =>  array_key_exists('l2', $row) ? number_format(floatval($row['l2']) , 2)." mm" : null,
            'l3'    =>  array_key_exists('l3', $row) ? number_format($row['l3'] , 2)." mm" : null,
            'z'     =>  array_key_exists('zahnezahl', $row) ? $row['zahnezahl'] : null,
            'D'    =>  array_key_exists('d_aussen', $row) ? number_format($row['d_aussen'] , 2)." mm" : null,
            'd'    =>  array_key_exists('d', $row) ? number_format($row['d'] , 2)." mm" : null,
            'B'    =>  array_key_exists('b', $row) ? number_format($row['b'] , 2)." mm" : null,
            'b1'    =>  array_key_exists('b1', $row) ? number_format($row['b1'] , 2)." mm" : null,
            'b2'    =>  array_key_exists('b2', $row) ? number_format($row['b2'] , 2)." mm" : null,
            'Torx'  => array_key_exists('torx', $row) ? $row['torx'] : null,
            'r'     => array_key_exists('eckradius', $row) ? number_format($row['eckradius'] , 2)." mm" : null,
        ];

        $partSpecificationsFiltered = array_filter($partSpecifications, function ($item) {
            return $item != null;
        });

        return json_encode($partSpecificationsFiltered);
    }

    public function getPartSpecificationValues($row)
    {
        $partSpecifications = [
            'm'     =>  array_key_exists('m', $row) ? $row['m'] : null,
            's'    =>  array_key_exists('s', $row) ? number_format($row['s'] , 2) : null,
            'p'    =>  array_key_exists('p', $row) ? number_format($row['p'] , 3) : null,
            't'    =>  array_key_exists('t', $row) ? number_format($row['t'] , 3) : null,
            'd1'    =>  array_key_exists('d1', $row) ? number_format($row['d1'] , 2) : null,
            'd2'    =>  $this->getLcd2($row) ? number_format($this->getLcd2($row) , 2) : null,
            'd3'    =>  array_key_exists('d3', $row) ? number_format($row['d3'] , 2) : null,
            'l1'    =>  array_key_exists('l1', $row) ? number_format($row['l1'] , 2) : null,
            'l2'    =>  array_key_exists('l2', $row) ? number_format(floatval($row['l2']), 2) : null,
            'l3'    =>  array_key_exists('l3', $row) ? number_format($row['l3'] , 2): null,
            'z'     =>  $this->getLcz($row),
            'D'    =>  array_key_exists('d_aussen', $row) ? number_format($row['d_aussen'] , 2) : null,
            'd'    =>  array_key_exists('d', $row) ? number_format($row['d'] , 2) : null,
            'B'    =>  array_key_exists('b', $row) ? number_format($row['b'] , 2) : null,
            'b1'    =>  array_key_exists('b1', $row) ? number_format($row['b1'] , 2) : null,
            'b2'    =>  array_key_exists('b2', $row) ? number_format($row['b2'] , 2) : null,
            'Torx'  => array_key_exists('torx', $row) ? $row['torx'] : null,
            'r'     => array_key_exists('eckradius', $row) ? number_format($row['eckradius'] , 2) : null,
        ];

        $partSpecificationsFiltered = array_filter($partSpecifications, function ($item) {
            return $item != null;
        });

        return json_encode($partSpecificationsFiltered);
    }

    public function getLcz($row) {
        if(array_key_exists('zahnezahl', $row) ) {
            return $row['zahnezahl'];
        }
        if(array_key_exists('zahnform_b', $row) ) {
            return $row['zahnform_b'];
        }
    }

    public function getLcd2($row) {
        if(array_key_exists('d2', $row) ) {
            if(str_contains($row['d2'], 'h')) {
                return explode('h',$row['d2'])[0];
            }
            else {
                return $row['d2'];
            }
        }
        return null;
    }

    public function getCustomsTariffNumbers($row)
    {
        $tariffNumber = str_replace('.', '', $row['zolltarifnummer']);
        $customSetting = $this->customsSettings->where('customs_tariff_number_eu', $tariffNumber)->first();

        return [
            'EU'    =>  $customSetting ? $customSetting->customs_tariff_number_eu : null,
            'CH'    =>  $tariffNumber
        ];
    }

    public function getSortValue($row)
    {
        if(array_key_exists('p', $row)) return $row['p'];
        if(array_key_exists('m', $row)) return $row['m'];
        if(array_key_exists('d1', $row)) return $row['d1'];
        if(array_key_exists('d_aussen', $row)) return $row['d_aussen'];
    }


    public function getImageAndDrawingLinks($row, $materialGroup)
    {
        $links = [];

        $links['table_detail_image_link'] = '';
        $links['image_links'] = null;

        if($materialGroup === '65051') {
            $links['table_detail_image_link'] = Arr::first($this->images, function ($value, $key) use ($row) {
                if (str_contains($value, 'Innengewindewirbler') &&str_contains($value, $row['zahnezahl'].'Z') ) {
                    return $value;
                }
            });

            $links['image_links'] = Arr::first($this->images, function ($value, $key) use ($row) {
                if (str_contains($value, 'Innengewindewirbler') &&str_contains($value, $row['zahnezahl'].'Z') ) {
                    return $value;
                }
            });
        }

        if($materialGroup === '65023') {
            $links['table_detail_image_link'] = Arr::first($this->images, function ($value, $key) use ($row) {
                if (str_contains($value, 'Kreissäge grobverzahnt')) {
                    return $value;
                }
            });
            $links['image_links'] = Arr::first($this->images, function ($value, $key) use ($row) {
                if (str_contains($value, 'Kreissäge grobverzahnt')) {
                    return $value;
                }
            });
        }

        if($materialGroup === '65013') {
            $links['table_detail_image_link'] = Arr::first($this->images, function ($value, $key) use ($row) {
                if (str_contains($value, 'Kreissäge feinverzahnt')) {
                    return $value;
                }
            });

            $links['image_links'] = Arr::first($this->images, function ($value, $key) use ($row) {
                if (str_contains($value, 'Kreissäge feinverzahnt')) {
                    return $value;
                }
            });
        }

        if($materialGroup === '65201') {
            $links['table_detail_image_link'] = Arr::first($this->images, function ($value, $key) use ($row) {
                if (str_contains($value, 'Automaten-Gewindefräser')) {
                    return $value;
                }
            });
            $links['image_links'] = Arr::first($this->images, function ($value, $key) use ($row) {
                if (str_contains($value, 'Automaten-Gewindefräser')) {
                    return $value;
                }
            });
        }

        if($materialGroup === '65153') {
            $links['table_detail_image_link'] = Arr::first($this->images, function ($value, $key) use ($row) {
                if (str_contains($value, 'Microfräser für Torx')) {
                    return $value;
                }
            });
            $links['image_links'] = Arr::first($this->images, function ($value, $key) use ($row) {
                if (str_contains($value, 'Microfräser für Torx')) {
                    return $value;
                }
            });
        }

        if($materialGroup === '65101') {
            $links['table_detail_image_link'] = Arr::first($this->images, function ($value, $key) use ($row) {
                if (str_contains($value, 'Gewindeschaftfräser ohne')) {
                    return $value;
                }
            });

            foreach ($this->images as $image) {
                if (str_contains($image, 'Gewindeschaftfräser ') ) {
                    if($links['image_links'] !== null) {
                        $links['image_links'] = $links['image_links'] . "," . $image;
                    }
                    else {
                        $links['image_links'] = $image;
                    }
                }
            }

        }
        return $links;
    }
}
