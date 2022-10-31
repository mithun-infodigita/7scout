<?php


namespace App\Imports\BasicImport;


use App\Models\Category\Category;
use App\Models\Column\Column;
use App\Models\Group\Group;
use App\Models\Producer\Producer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class diametal_basic_import implements ToCollection, WithHeadingRow
{
    public $import;


    public function basicImport($import)
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
        $producer = Producer::where('unique_id', 'diametal')->first();
        foreach ($rows as $row)
        {

            if (DB::table( $this->import->producer->unique_id . '_parts_' .  $this->import->language)->where('part_id',  $this->import->producer->unique_id . '_' . $row['ressource'])->count()) {
                DB::table( $this->import->producer->unique_id . '_parts_' .  $this->import->language)->where('part_id',  $this->import->producer->unique_id . '_' . $row['ressource'])
                    ->update([
                        'full_record' => json_encode($row),
                        'stock'                     =>  json_encode([ "2" =>  rand(0, 100), "3" =>  rand(0, 100), ]),
                        'reprocurement_time'         =>  rand(0, 30)
                    ]);
            } else {
                DB::table( $this->import->producer->unique_id . '_parts_' .  $this->import->language)->insert([
                    'part_id' =>  $this->import->producer->unique_id . '_' . $row['ressource'],
                    'producer_name' => $producer->name,
                    'producer_id'   =>  $producer->unique_id,
                    'full_record' => json_encode($row),
                    'stock'                     =>  json_encode([ "2" =>  rand(0, 100), "3" =>  rand(0, 100), ]),
                    'reprocurement_time'         =>  rand(0, 30)
                ]);
            }



        }
    }
}
