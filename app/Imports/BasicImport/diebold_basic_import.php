<?php


namespace App\Imports\BasicImport;

use Illuminate\Support\Facades\DB;

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

            $item = [];
            foreach ($xml->FAMILIE as $family) {

                $item['familyId']   =  (string)$family->attributes()->ID;

                foreach ($family->GRUPPE as $group) {
                    $item['groupId']   =  (string)$group->attributes()->ID;
                    $item['images'] = [];
                    $acceptedExtensions = ['jpg', 'JPG', 'png', 'PNG'];
                    foreach ($group->BILD as $picture) {
                        if(in_array(pathinfo($picture)['extension'], $acceptedExtensions)){
                            $item['imageLink'] = urlencode($picture);
                        }
                        array_push($item['images'], urlencode($picture));
                    }

                    foreach ($group->ARTIKEL as $part) {
                            $item['partId'] = (string)$part->attributes()->ID;
                            $item['price']  =   [
                                'currency'  =>  'EUR',
                                'value' => str_replace(",",".", (string)$part->Preis )
                            ];

                            $featureKeys = [];
                            foreach ($part->MERKMAL as $feature) {
                                array_push($featureKeys,(string)$feature->KUERZEL);
                                $item[(string)$feature->KUERZEL] = (string)$feature->WERT;
                            }

                            foreach ($part->TEXT as $text) {

                                if ((string)$text->attributes()->Typ === 'ArtBez') {
                                    $item['partName']['de'] = (string)$text->DE;
                                    $item['partName']['en'] = (string)$text->EN;
                                }

                                if ((string)$text->attributes()->Typ === 'InfoB') {
                                    $item['info']['de'] = html_entity_decode(strip_tags($text->DE));
                                    $item['info']['en'] = html_entity_decode(strip_tags($text->EN));
                                }

                            }

                            if (DB::table($import->producer->unique_id . '_parts_' . $import->language)->where('part_id', $import->producer->unique_id . '_' . $part->attributes()->ID)->count()) {
                                DB::table($import->producer->unique_id . '_parts_' . $import->language)->where('part_id', $import->producer->unique_id . '_' . $part->attributes()->ID)
                                    ->update([
                                        'full_record' => json_encode($item),
                                        'part_number' => $part->attributes()->ID,
                                        'producer_id'       =>  'diebold',
                                        'stock'                     =>  json_encode([ "4" =>  10]),
                                        'reprocurement_time'         =>  21

                                    ]);
                            } else {
                                DB::table($import->producer->unique_id . '_parts_' . $import->language)->insert([
                                    'part_id' => $import->producer->unique_id . '_' . $part->attributes()->ID,
                                    'part_number' => $part->attributes()->ID,
                                    'full_record' => json_encode($item),
                                    'producer_id'       =>  'diebold',
                                    'stock'                     =>  json_encode([ "4" =>  10]),
                                    'reprocurement_time'         => 21

                                ]);
                            }
                            unset($item['partId']);
                            unset($item['partName']);
                            unset($item['description']);
                            unset($item['info']);
                            unset($item['info']);

                            foreach ($featureKeys as $featureKey) {
                                unset($item[$featureKey]);
                            }

                        }
                        unset($item['imageLink']);
                    }
               // }

            }

        }

    }

}
