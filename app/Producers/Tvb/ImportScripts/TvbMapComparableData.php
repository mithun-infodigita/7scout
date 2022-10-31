<?php

namespace App\Producers\Tvb\ImportScripts;

use App\Producers\Tvb\Models\TvbPartsDe;
use App\Producers\Tvb\Models\TvbPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class TvbMapComparableData implements ToCollection, WithHeadingRow
{
    public $import;


    public function mapData($import)
    {
        $this->import = $import;
        $files = $import->getMedia();

        foreach ($files as $file) {
            if (file_exists($file->getPath())) {
                Excel::import($this, $file->getPath());
            }
        }
    }
    public function collection(Collection $rows)
    {
        $this->mapComparableData($rows);
    }

    public function mapComparableData($rows) {
        switch ($this->import->language) {
            case 'de':
                $this->importGermanData($rows);
                break;
            case 'en':
                $this->importEnglishData($rows);
                break;
        }
    }

    public function importGermanData($rows)
    {
        foreach ($rows as $row) {
            $part = TvbPartsDe::where('part_id', 'tvb_'.$row['artikelnummer'])->first();

            if($part) {
                $part->update($this->getAttributes($row));
            }
        }
    }

    public function importEnglishData($rows)
    {
        foreach ($rows as $row) {
            $part = TvbPartsEn::where('part_id', 'tvb_'.$row['artikelnummer'])->first();

            if($part) {
                $part->update($this->getAttributes($row));
            }
        }
    }

    public function getAttributes($row) {

        $specifications = explode('I', $row['spezifikationen']);

        $partSpecifications = [];

        foreach ($specifications as $specification) {

            if(Str::of($specification)->trim()->isNotEmpty()) {
                $partSpecifications[trim(explode(':', $specification)[0])] = trim(explode(':', $specification)[1]);
            }
        }

        return [
            'cutting_edge_diameter'             =>  array_key_exists('d', $partSpecifications) ? number_format($partSpecifications['d'] , 2)." mm" : null,
            'cutting_edge_diameter_value'       =>  array_key_exists('d', $partSpecifications) ? number_format($partSpecifications['d'] , 2) : null,
            'overall_length'                    =>  array_key_exists('L', $partSpecifications) ? number_format($partSpecifications['L'] , 2)." mm" : null,
            'overall_length_value'              =>  array_key_exists('L', $partSpecifications) ? number_format($partSpecifications['L'] , 2) : null,
            'flute_length'                      =>  array_key_exists('ln', $partSpecifications) ? number_format($partSpecifications['L'] , 2)." mm" : null,
            'flute_length_value'                =>  array_key_exists('ln', $partSpecifications) ? number_format($partSpecifications['L'] , 2) : null,
            'shank_diameter'                    =>  array_key_exists('D', $partSpecifications) ? number_format($partSpecifications['D'] , 2)." mm" : null,
            'shank_diameter_value'              =>  array_key_exists('D', $partSpecifications) ? number_format($partSpecifications['D'] , 2) : null,
        ];
    }
}
