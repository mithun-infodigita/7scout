<?php

namespace App\Producers\Botek\ImportScripts;

use App\Producers\Botek\Models\BotekPartsDe;
use App\Producers\Botek\Models\BotekPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class BotekMapComparableData implements ToCollection, WithHeadingRow
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
            $part = BotekPartsDe::where('part_id', 'botek_'.$row['artikel'])->first();

            if($part) {
                $part->update($this->getAttributes($row->toArray()));
            }
        }
    }

    public function importEnglishData($rows)
    {
        foreach ($rows as $row) {
            $part = BotekPartsEn::where('part_id', 'botek_'.$row['artikel'])->first();

            if($part) {
                $part->update($this->getAttributes($row));
            }
        }
    }

    public function getAttributes($row)
    {

        return [
            'cutting_edge_diameter'             =>  array_key_exists('durchmesser_dc', $row) && $row['durchmesser_dc']? number_format($row['durchmesser_dc'] , 2)." mm" : null,
            'cutting_edge_diameter_value'       =>  array_key_exists('durchmesser_dc', $row) && $row['durchmesser_dc']? number_format($row['durchmesser_dc'] , 2) : null,
            'overall_length'                    =>  array_key_exists('gesamtlaenge_oal', $row) && $row['gesamtlaenge_oal']? number_format($row['gesamtlaenge_oal'] , 2)." mm" : null,
            'overall_length_value'              =>  array_key_exists('gesamtlaenge_oal', $row) && $row['gesamtlaenge_oal']? number_format($row['gesamtlaenge_oal'] , 2) : null,
            'flute_length'                    =>  array_key_exists('spannutlaenge_lcf', $row) && $row['spannutlaenge_lcf']? number_format($row['spannutlaenge_lcf'] , 2)." mm" : null,
            'flute_length_value'              =>  array_key_exists('spannutlaenge_lcf', $row) && $row['spannutlaenge_lcf']? number_format($row['spannutlaenge_lcf'] , 2) : null,
            'shank_diameter'                    =>  array_key_exists('schaftdurchmesser_dcon', $row) && $row['schaftdurchmesser_dcon']? number_format($row['schaftdurchmesser_dcon'] , 2)." mm" : null,
            'shank_diameter_value'              =>  array_key_exists('schaftdurchmesser_dcon', $row) && $row['schaftdurchmesser_dcon']? number_format($row['schaftdurchmesser_dcon'] , 2) : null,
        ];
    }
}
