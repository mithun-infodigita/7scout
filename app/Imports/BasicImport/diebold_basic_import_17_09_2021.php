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
        $num = 0;
        if (file_exists($file->getPath())) {

            $json = file_get_contents($file->getPath());

            $data = json_decode($json, true);
            $item = [];
            foreach ($data['Projekt']['FAMILIE'] as $familie) {

                $item['FAMILIE_ID'] = $familie['_ID'];

                if (array_key_exists('GRUPPE', $familie)) {

                    foreach ($familie['GRUPPE'] as $gruppe_key => $gruppe_value) {
                        $imageLink = '';
                        echo "----".$gruppe_value['StammartikelNr']."------";
                        if (is_array($gruppe_value) && array_key_exists('BILD', $gruppe_value)) {
                            if (is_array($gruppe_value['BILD'])) {
                                $imageLink = urlencode($gruppe_value['BILD'][0]);
                            } else {
                                $imageLink = urlencode($gruppe_value['BILD']);
                            }

                        }


                        if (is_array($gruppe_value) && array_key_exists('TEXT', $gruppe_value)) {
                            foreach ($gruppe_value['TEXT'] as $text_value) {

                                if (is_array($text_value) && array_key_exists('_Typ', $text_value)) {
                                    if (array_key_exists('DE', $text_value)) {
                                        $item['Gruppe_' . $text_value["_Typ"]]['DE'] = $text_value["DE"];
                                    }
                                    if (array_key_exists('EN', $text_value)) {
                                        $item['Gruppe_' . $text_value["_Typ"]]['EN'] = $text_value["EN"];
                                    }
                                }
                            }


                            if (is_array($gruppe_value) && array_key_exists('ARTIKEL', $gruppe_value)) {

                                foreach ($gruppe_value['ARTIKEL'] as $artikel_key => $artikel_value) {
                                    if (is_array($artikel_value) && array_key_exists('_ID', $artikel_value)) {
                                        $item['ARTIKEL_ID'] = $artikel_value['_ID'];
                                        $fullRecord = $artikel_value;
                                        echo $item['ARTIKEL_ID'] . "<br>";
                                    }
                                    else {
                                        var_dump($artikel_value);
                                        $item['ARTIKEL_ID'] = $artikel_value['_ID'];
                                    }


                                    if ($artikel_key === 'TEXT') {

                                        if (array_key_exists('DE', $artikel_value)) {
                                            $item['Artikelbezeichnung']['DE'] = $artikel_value["DE"];
                                        }
                                        if (array_key_exists('EN', $artikel_value)) {
                                            $item['Artikelbezeichnung']['EN'] = $artikel_value["EN"];
                                        }
                                    }

//                                if ($artikel_key === 'MERKMAL') {
//
//                                    foreach ($artikel_value as $merkmal) {
//
//                                        if (is_array($merkmal) && array_key_exists('KUERZEL',$merkmal )) {
//                                            $item['Merkmal'] = [
//                                                $merkmal["KUERZEL"] => $merkmal['WERT']
//                                            ];
//                                        }
//                                    }
//                                }
//
//                                if(array_key_exists('ARTIKEL_ID', $item)) {
//                                    if (DB::table($import->producer->unique_id . '_parts_' . $import->language)->where('part_id', $import->producer->unique_id . '_' . $item['ARTIKEL_ID'])->count()) {
//                                        DB::table($import->producer->unique_id . '_parts_' . $import->language)->where('part_id', $import->producer->unique_id . '_' . $item['ARTIKEL_ID'])
//                                            ->update([
//                                                'full_record' => json_encode($fullRecord),
//                                                'data_1'   =>   json_encode($item),
//                                                'part_number' => $item['ARTIKEL_ID'],
//                                                'image_link' => $imageLink
//
//                                            ]);
//                                    } else {
//                                        DB::table($import->producer->unique_id . '_parts_' . $import->language)->insert([
//                                            'part_id' => $import->producer->unique_id . '_' . $item['ARTIKEL_ID'],
//                                            'part_number' => $item['ARTIKEL_ID'],
//                                            'full_record' => json_encode($fullRecord),
//                                            'data_1'   =>   json_encode($item),
//                                            'image_link' => $imageLink
//                                        ]);
//                                    }
//
//                                    $item = [];
//                                }
                                }
                            }
                        }


//                        if (is_array($gruppe_value) && array_key_exists('ARTIKEL', $gruppe_value)) {
//
//                            foreach ($gruppe_value['ARTIKEL'] as $artikel_key => $artikel_value) {
//                                if ($artikel_key === 'TEXT') {
//
//                                    if (array_key_exists('DE', $artikel_value['TEXT'])) {
//                                        $item['Artikel_' . $artikel_value["_Typ"]]['DE'] = $artikel_value['TEXT']["DE"];
//                                    }
//                                    if (array_key_exists('EN', $artikel_value['TEXT'])) {
//                                        $item['Artikel_' . $artikel_value["_Typ"]]['EN'] = $artikel_value['TEXT']["EN"];
//                                    }
//                                }


//
//                                if(is_array($artikel_value) && array_key_exists('TEXT', $artikel_value)) {
//
//                                    foreach ($artikel_value['TEXT'] as $text_value) {
//
//                                        if(is_array($text_value) && array_key_exists('_Typ', $text_value)) {
//                                            if(array_key_exists('DE', $text_value)) {
//                                                $item['Artikel_'.$text_value["_Typ"]]['DE'] = $text_value["DE"];
//                                            }
//                                            if(array_key_exists('EN', $text_value)) {
//                                                $item['Artikel_'.$text_value["_Typ"]]['EN'] = $text_value["EN"];
//                                            }
//                                        }
//                                    }
//
//
//
//                                }
                    }


                }

            }


        }


    }



}
