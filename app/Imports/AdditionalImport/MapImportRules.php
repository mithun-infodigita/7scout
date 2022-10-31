<?php


namespace App\Imports\AdditionalImport;


use App\Models\Column\Column;
use App\Models\Producer\Producer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class MapImportRules implements ToCollection, WithHeadingRow
{
    public $import;


    public function mapData($import)
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

        foreach ($this->import->importRules as $importRule) {

            $sourceReferenceColumn = Column::where('id', $importRule->source_reference_column_id)->first()->name;
            $mapColumn = Column::where('id', $importRule->map_column_id)->first()->name;


            foreach ($rows as $row) {

                DB::table($this->import->producer->unique_id . '_parts_' . $this->import->language)->where($sourceReferenceColumn, $this->import->producer->unique_id .'_'.eval("return ".$importRule->map_reference_script.";"))
                    ->update([
                        $mapColumn => eval("return ".$importRule->map_value_script.";")
                    ]);
            }
        }

    }
}

