<?php

namespace App\Producers\Nachreiner\ImportScripts;

use App\Producers\Nachreiner\Models\NachreinerPartsDe;
use App\Producers\Nachreiner\Models\NachreinerPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class NachreinerMapComparableData implements ToCollection, WithHeadingRow
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
            $part = NachreinerPartsDe::where('part_id', 'nachreiner_'.$row['suchwort'])->first();

            if($part) {
                $part->update($this->getAttributes($row));
            }
        }
    }

    public function importEnglishData($rows)
    {
        foreach ($rows as $row) {
            $part = NachreinerPartsEn::where('part_id', 'nachreiner_'.$row['suchwort'])->first();

            if($part) {
                $part->update($this->getAttributes($row));
            }
        }
    }

    public function getAttributes($row) {
        return [
            'cutting_edge_diameter'             =>  number_format($row['durchmesser'] , 2)." mm",
            'cutting_edge_diameter_value'       =>  number_format($row['durchmesser'] , 2),
            'overall_length'                    =>  number_format($row['gesamtlange'] , 2)." mm",
            'overall_length_value'              =>  number_format($row['gesamtlange'] , 2),
            'flute_length'                      =>  number_format($row['schneidelange'] , 2)." mm",
            'flute_length_value'                =>  number_format($row['schneidelange'] , 2),
            'shank_diameter'                    =>  number_format($row['schaftdurchmesser'] , 2)." mm",
            'shank_diameter_value'              =>  number_format($row['schaftdurchmesser'] , 2),
            'number_of_cutting_edges'           =>  $row['zahnezahl']  > 0 ? number_format($row['zahnezahl'] , 0) : null,
            'shank_type'                        =>  $row['schaftart'],
            'coating'                           =>  $row['beschichtung'],
            'standard'                          =>  'DIN '.$row['din'],
            'tolerance_nominal'                 =>  $row['passung'],
            'thread'                            =>  $row['abmessunggewinde']
        ];
    }
}
