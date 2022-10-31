<?php

namespace App\Producers\Voelkel\ImportScripts;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class VoelkelTextImport implements ToCollection, WithHeadingRow
{
    public function getTerms($import)
    {
        $filePath = $import->getMedia('additionalFiles')->where('name', 'DE-EN Begriffe V009')->first()->getPath();

        $terms = Excel::toArray($this, $filePath);

        return collect($terms[0]);
    }

    public function getTexts($import)
    {
        $filePath = $import->getMedia('additionalFiles')->where('name', 'DE-EN Texte V010')->first()->getPath();

        $texts = Excel::toArray($this, $filePath);

        return collect($texts[0]);
    }


    public function collection(Collection $rows)
    {

    }
}
