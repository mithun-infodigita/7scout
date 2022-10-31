<?php


namespace App\Producers\Nachreiner\ImportScripts;

use App\Models\CustomsSetting\CustomsSetting;
use App\Producers\Nachreiner\Models\NachreinerPartsDe;
use App\Producers\Nachreiner\Models\NachreinerPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class NachreinerMasterDataImport implements ToCollection, WithHeadingRow
{
    public $import;

    public function masterDataImport($import)
    {
        $this->import = $import;
        $files = $import->getMedia('additionalFiles');

        foreach ($files as $file) {
            if (file_exists($file->getPath())) {
                Excel::import($this, $file->getPath());
            }
        }
    }

    public function collection(Collection $rows)
    {
        $this->importMasterData($rows);
    }

    public function importMasterData($rows) {
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
            $basicData = NachreinerPartsDe::where('part_id', 'like', '%'.$row['suchwort'].'%')->get();

            foreach ($basicData as $data) {
                $data->update([
                    'part_name'                  =>      $row['text_in_deutsch'],
                    'table_detail_image_link'    =>      $row['bild_1'],
                    'image_links'                =>      json_encode([$row['bild_1'], $row['bild_2']])
                ]);
            }
        }
    }

    public function importEnglishData($rows)
    {
        foreach ($rows as $row) {
            $basicData = NachreinerPartsEn::where('part_id', 'like', '%'.$row['suchwort'].'%')->get();

            foreach ($basicData as $data) {
                $data->update([
                    'part_name'                     =>      $row['text_in_englisch'],
                    'table_detail_image_link'       =>      $row['bild_1'],
                    'image_links'                   =>      json_encode([$row['bild_1'], $row['bild_2']])
                ]);
            }
        }
    }

}
