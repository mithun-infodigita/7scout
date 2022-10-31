<?php

namespace App\Producers\Diametal\ImportScripts;

use App\Producers\Diametal\Models\DiametalPartsDe;
use App\Producers\Diametal\Models\DiametalPartsEn;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class DiametalMapComparableData implements ToCollection, WithHeadingRow, WithCustomCsvSettings
{
    public $import;

    public function mapData($import)
    {
        $this->import = $import;

        $files = Storage::disk('localDiametalMasterData')->allFiles();

        foreach ($files as $file) {
            Excel::import($this, storage_path('/app/public/producers/diametal/masterData/'. $file));
        }
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => "|"
        ];
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
            $part = DiametalPartsDe::where('part_id', 'diametal' . '_' . $row['artikelnr'])->first();

            if($part) {
                $part->update($this->getAttributes($row));
            }
        }
    }

    public function importEnglishData($rows)
    {
        foreach ($rows as $row) {
            $part = DiametalPartsEn::where('part_id', 'diametal' . '_' . $row['artikelnr'])->first();

            if($part) {
                $part->update($this->getAttributes($row));
            }
        }
    }

    public function getAttributes($row)
    {

        return [
            'cutting_edge_diameter'             =>  $row['d1'] ? number_format($row['d1'] , 2)." mm" : null,
            'cutting_edge_diameter_value'       =>  $row['d1'] ? number_format($row['d1'] , 2) : null,
            'overall_length'                    =>  $row['l2'] ? number_format($row['l2'] , 2)." mm" : null,
            'overall_length_value'              =>  $row['l2'] ?  number_format($row['l2'] , 2) : null,
            'flute_length'                      =>  $row['l1'] ? number_format($row['l1'] , 2)." mm" : null,
            'flute_length_value'                =>  $row['l1'] ? number_format($row['l1'] , 2) : null,
            'shank_diameter'                    =>  $row['d2'] ? number_format($row['d2'] , 2)." mm" : null,
            'shank_diameter_value'              =>  $row['d2'] ?  number_format($row['d2'] , 2) : null,
            'coating'                           =>  $row['beschichtung'] ? $row['beschichtung'] : null,
            'thread'                            =>  $row['gewinde'] ? $row['gewinde'] : null,
            'pitch'                             =>  $row['steigung'] ? number_format($row['steigung'] , 2)." mm" : null,
            'pitch_value'                       =>  $row['steigung'] ? number_format($row['steigung'] , 2) : null,
        ];

    }
}
