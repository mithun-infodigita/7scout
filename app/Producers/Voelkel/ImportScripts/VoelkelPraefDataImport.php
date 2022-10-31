<?php

namespace App\Producers\Voelkel\ImportScripts;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class VoelkelPraefDataImport implements ToCollection, WithHeadingRow
{
    public function getPraefData($import)
    {
        $filePath = $import->getMedia('additionalFiles')->where('name', 'Artikelliste Ursprung 20210920')->first()->getPath();

        $data = Excel::toArray($this, $filePath);

        return collect($data[0]);
    }



    public function collection(Collection $rows)
    {

    }
}
