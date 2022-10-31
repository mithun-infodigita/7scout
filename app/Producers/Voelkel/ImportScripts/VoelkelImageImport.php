<?php

namespace App\Producers\Voelkel\ImportScripts;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class VoelkelImageImport implements ToCollection, WithHeadingRow
{
    public function getImagess($import)
    {
        $filePath = $import->getMedia('additionalFiles')->where('name', 'SML Bilddaten V006')->first()->getPath();

        $terms = Excel::toArray($this, $filePath);

        return collect($terms[0]);
    }


    public function collection(Collection $rows)
    {

    }
}
