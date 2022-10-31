<?php


namespace App\Imports\BasicImport;

use App\Models\Producer\Producer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class nachreiner_basic_import implements ToCollection, WithHeadingRow
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
        $producer = Producer::where('unique_id', 'nachreiner')->first();
        foreach ($rows as $row)
        {

            if (DB::table( $this->import->producer->unique_id . '_parts_' .  $this->import->language)->where('part_id',  $this->import->producer->unique_id . '_' . $row['suchwort'])->count()) {
                DB::table( $this->import->producer->unique_id . '_parts_' .  $this->import->language)->where('part_id',  $this->import->producer->unique_id . '_' . $row['suchwort'])
                    ->update([
                        'full_record' => json_encode($row),
                        'stock'                     =>  json_encode([ "1" =>  0]),
                        'reprocurement_time'         =>  21
                    ]);
            } else {
                DB::table( $this->import->producer->unique_id . '_parts_' .  $this->import->language)->insert([
                    'part_id' =>  $this->import->producer->unique_id . '_' . $row['suchwort'],
                    'producer_name' => $producer->name,
                    'producer_id'   =>  $producer->unique_id,
                    'full_record' => json_encode($row),
                    'stock'                     =>  json_encode([ "1" =>  0]),
                    'reprocurement_time'         =>  21
                ]);
            }
        }
    }
}
