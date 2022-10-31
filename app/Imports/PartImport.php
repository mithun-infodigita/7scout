<?php


namespace App\Imports;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class PartImport implements ToCollection
{

    public $importId;

    public function __construct($importId)
    {
        $this->importId = $importId;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $row)
        {
            DB::table('import_parts_'.$this->importId)->insert(
                ['imported_data' => $row]
            );
        }
    }
}
