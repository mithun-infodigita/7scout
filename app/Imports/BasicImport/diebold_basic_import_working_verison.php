<?php


namespace App\Imports\BasicImport;


use App\Models\Category\Category;
use App\Models\Column\Column;
use App\Models\Group\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class diebold_basic_import
{
    public function basicImport($import)
    {
        $files = $import->getMedia();

        foreach ($files as $file) {
            $this->importFile($import, $file);
        }


    }

    public function importFile($import, $file)
    {
        if (file_exists($file->getPath())) {
            $xml = simplexml_load_file($file->getPath());

            $num = 1;

            foreach ($xml->FAMILIE as $familie) {

                foreach ($familie->GRUPPE as $gruppe) {

                    $imageLink = urlencode($gruppe->BILD);

                    foreach ($gruppe->ARTIKEL as $part) {

                        $merkmal = [];


                        foreach ($part as $m) {
                            if($m->KUERZEL) {

                                $name = (string)$m->KUERZEL;
                                $merkmal[$name] = $m;
                            }
                        }

                        if (DB::table($import->producer->unique_id . '_parts_' . $import->language)->where('part_id', $import->producer->unique_id . '_' . $part->attributes()->ID)->count()) {
                            DB::table($import->producer->unique_id . '_parts_' . $import->language)->where('part_id', $import->producer->unique_id . '_' . $part->attributes()->ID)
                                ->update([
                                    'full_record' => json_encode($part),
                                    'data_1'   =>   json_encode($merkmal),
                                    'part_number' => $part->attributes()->ID,
                                    'image_link'    =>  $imageLink

                            ]);
                        } else {
                            DB::table($import->producer->unique_id . '_parts_' . $import->language)->insert([
                                'part_id' => $import->producer->unique_id . '_' . $part->attributes()->ID,
                                'part_number' => $part->attributes()->ID,
                                'full_record' => json_encode($part),
                                'data_1'   =>   json_encode($merkmal),
                                'image_link'    =>  $imageLink
                            ]);
                        }

                        $num++;
                    }
                }
            }
        }

    }

}
