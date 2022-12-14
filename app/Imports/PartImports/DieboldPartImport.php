<?php


namespace App\Imports\PartImports;


use App\Models\Category\Category;
use App\Models\Column\Column;
use App\Models\Group\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DieboldPartImport
{
    public function mapData($import)
    {
        $files = $import->getMedia();
        foreach ($files as $file) {
            if (file_exists($file->getPath())) {
                $xml = simplexml_load_file($file->getPath());

                $num = 1;

                foreach ($xml->FAMILIE as $familie) {



                    foreach ($familie->GRUPPE as $gruppe) {

                        foreach ($gruppe->ARTIKEL as $part) {

                            DB::table('import_parts_'.$import->id)->upsert(
                                $this->getColumns($part, $import),
//                                [
//                                    'vendor_id' => 'diebold',
//                                    'vendor_name' => 'Diebold',
//                                    'part_id' => $part->attributes()->ID,
//                                    'part_name' =>  $part->TEXT->DE,
//                                    'full_record' => json_encode($part),
//                                    //'group' => $this->getGroup($part),
//                                    'cat_level_1' =>    $this->mapColumn($part, 'cat_level_1', $import->importRules),
//                                    'cat_level_2' =>    $this->mapColumn($part, 'cat_level_2', $import->importRules),
//                                    'cat_level_3' =>    $this->mapColumn($part, 'cat_level_3', $import->importRules),
//
//                                ],
                                config('columns.reference_id'),
                                Column::where('import_parts_table', 1)->pluck('name')->toArray(),
                            );

                            $num++;
                        }
                    }
                }
                //var_dump($xml->FAMILIE->GRUPPE->ARTIKEL[1]);
            } else {
                exit('Konnte test.xml nicht ??ffnen.');
            }
        }
    }

    public function getColumns($part, $import) {
        $basicColumns = Column::where('import_parts_table', 1)->get();

        $language = $import->language;

        foreach ($basicColumns as $column) {
            $columns[$column->name] = $this->mapColumn($part, $column->name, $import->importRules, $language);
        }


        if($columns['cat_level_5_id']) {
            $columns['cat_level_5_name'] = Category::where('id', $columns['cat_level_5_id'])->first()->$language;
            $parentId = Category::where('id', $columns['cat_level_5_id'])->first()->parent_id;
            $parent = Category::where('id', $parentId)->first();
            $columns['cat_level_4_id'] = $parentId;
            $columns['cat_level_4_name'] = $parent->$import->language;
        }

        if($columns['cat_level_4_id']) {
            $columns['cat_level_4_name'] = Category::where('id', $columns['cat_level_4_id'])->first()->$language;
            $parentId = Category::where('id', $columns['cat_level_4_id'])->first()->parent_id;
            $parent = Category::where('id', $parentId)->first();
            $columns['cat_level_3_id'] = $parentId;
            $columns['cat_level_3_name'] = $parent->$language;
        }

        if($columns['cat_level_3_id']) {
            $columns['cat_level_3_name'] = Category::where('id', $columns['cat_level_3_id'])->first()->$language;
            $parentId = Category::where('id', $columns['cat_level_3_id'])->first()->parent_id;
            $parent = Category::where('id', $parentId)->first();
            $columns['cat_level_2_id'] = $parentId;
            $columns['cat_level_2_name'] = $parent->$language;
        }

        if($columns['cat_level_2_id']) {
            $columns['cat_level_2_name'] = Category::where('id', $columns['cat_level_2_id'])->first()->$language;
            $parentId = Category::where('id', $columns['cat_level_2_id'])->first()->parent_id;
            $parent = Category::where('id', $parentId)->first();
            $columns['cat_level_1_id'] = $parentId;
            $columns['cat_level_1_name'] = $parent->$language;
        }

        if($columns['cat_level_1_id']) {
            $columns['cat_level_1_name'] = Category::where('id', $columns['cat_level_1_id'])->first()->$language;
        }

        if($columns['group_id']) {
            $columns['group_name'] = Group::where('id', $columns['group_id'])->first()->$language;
        }

        return $columns;
    }

    public function mapColumn($part, $column, $importRules, $language) {
        $val = null;
        $columnRules = $importRules->where('column', $column);
        foreach ($columnRules as $rule) {

            if($rule->rule_type=== 'fix_text') {
                $val = $rule->text_value;
            }

            if($rule->rule_type === 'compare_text') {
                if ($rule->compare_type === 'contains') {
                    if (Str::contains(eval("return " . $rule->part_value_script . ";"), $rule->compare_value)) {
                        $val = eval("return ".$rule->map_value_script.";");
                    }
                }
            }

            if($rule->rule_type=== 'map') {
                $val = eval("return ".$rule->map_value_script.";");
            }

            if($rule->rule_type === 'category') {
                if ($rule->compare_type === 'contains') {
                    if (Str::contains(eval("return " . $rule->part_value_script . ";"), $rule->compare_value)) {
                        if(Str::contains($column, '_id')) {
                            $val = $rule->category_id;
                        }
                        else {
                            $val = Category::where('id', $rule->category_id)->first()->$language;
                        }
                    }
                }
            }

            if($rule->rule_type === 'group') {
                if ($rule->compare_type === 'contains') {
                    if (Str::contains(eval("return " . $rule->part_value_script . ";"), $rule->compare_value)) {
                        if(Str::contains($column, '_id')) {
                            $val = $rule->group_id;
                        }
                        else {
                            $val = Group::where('id', $rule->group_id)->first()->$language;
                        }
                    }
                }
            }

            if($rule->rule_type === 'full_record') {
                $val = json_encode($part);
            }

        }

        return $val;
    }

    public function getValue($part, $rule) {
        $val = null;
            if($rule->rule_type === 'category') {
                $val = Category::where('id', $rule->category_id)->first()->de;
            }

            if($rule->rule_type === 'group') {
                $val = Group::where('id', $rule->group_id)->first()->de;
            }

            if($rule->rule_type === 'map') {
                $val = eval("return ".$rule->map_value_script.";");
            }

            if($rule->rule_type === 'text') {
                $val = $rule->text_value;
            }

        return $val;
    }


    public function getGroup($part)
    {
        if (Str::contains($part->TEXT->DE, '3-D Kantentaster')) {return '3-D Kantentaster';}
        if (Str::contains($part->TEXT->DE, 'Anzugsbolzen')) {return 'Anzugsbolzen';}
        if (Str::contains($part->TEXT->DE, 'Ferritscheibe')) {return 'Ferritscheibe';}
        if (Str::contains($part->TEXT->DE, 'Hydrodehnspannf')) {return 'Hydrodehnspannfutter';}
        if (Str::contains($part->TEXT->DE, 'Spannmutter')) {return 'Spannmutter';}
        if (Str::contains($part->TEXT->DE, 'Schrumpffutter')) {return 'Schrumpffutter';}
        if (Str::contains($part->TEXT->DE, 'Spannzangenfutter')) {return 'Spannzangenfutter';}
        if (Str::contains($part->TEXT->DE, 'Spannzange')) {return 'Spannzange';}
        if (Str::contains($part->TEXT->DE, 'Reduzierh??lse')) {return 'Reduzierh??lse';}
        if (Str::contains($part->TEXT->DE, 'Weldon')) {return 'Weldon';}
        if (Str::contains($part->TEXT->DE, 'Wuchtmeister')) {return 'Wuchtmeister';}
        if (Str::contains($part->TEXT->DE, 'Aufsteckfr??serdorn')) {return 'Aufsteckfr??serdorn';}
        if (Str::contains($part->TEXT->DE, 'Dichtscheibe')) {return 'Dichtscheibe';}
        if (Str::contains($part->TEXT->DE, 'Drehmomentschl??ssel')) {return 'Drehmomentschl??ssel';}
        if (Str::contains($part->TEXT->DE, 'Kombi-Aufsteckfd')) {return 'Kombi-Aufsteckfdorn';}
        if (Str::contains($part->TEXT->DE, 'K??hlmittel??bergaberohr')) {return 'K??hlmittel??bergaberohr';}
        if (Str::contains($part->TEXT->DE, 'NC-Bohrfutter')) {return 'NC-Bohrfutter';}
        if (Str::contains($part->TEXT->DE, 'Pr??fdorn')) {return 'Pr??fdorn';}
        if (Str::contains($part->TEXT->DE, 'Rohling')) {return 'Rohling';}
        if (Str::contains($part->TEXT->DE, 'Schnellwechseleinsatz')) {return 'Schnellwechseleinsatz';}
        if (Str::contains($part->TEXT->DE, 'Schrumpfspannzange')) {return 'Schrumpfspannzange';}
        if (Str::contains($part->TEXT->DE, 'Abdr??ckvorrichtung')) {return 'Abdr??ckvorrichtung';}
        if (Str::contains($part->TEXT->DE, 'Aufsteckdorn')) {return 'Aufsteckdorn';}
        if (Str::contains($part->TEXT->DE, 'Blindstopfen')) {return 'Blindstopfen';}
        if (Str::contains($part->TEXT->DE, 'Dehnspannfutter')) {return 'Dehnspannfutter';}
        if (Str::contains($part->TEXT->DE, 'D??senring')) {return 'D??senring';}
        if (Str::contains($part->TEXT->DE, 'Fr??seranzugsschraube')) {return 'Fr??seranzugsschraube';}
        if (Str::contains($part->TEXT->DE, 'Gewindeschneidfutter')) {return 'Gewindeschneidfutter';}
        if (Str::contains($part->TEXT->DE, 'Gleitringspannmutter')) {return 'Gleitringspannmutter';}
        if (Str::contains($part->TEXT->DE, 'Induktiv-Schrumpfger??t ')) {return 'Induktiv-Schrumpfger??t ';}
        if (Str::contains($part->TEXT->DE, 'Kraftspannfutter')) {return 'Kraftspannfutter';}
        if (Str::contains($part->TEXT->DE, 'L??ngeneinstellh??lse')) {return 'L??ngeneinstellh??lse';}
        if (Str::contains($part->TEXT->DE, 'Mitnehmerring')) {return 'Mitnehmerring';}
        if (Str::contains($part->TEXT->DE, 'Nutensteine')) {return 'Nutensteine';}
        if (Str::contains($part->TEXT->DE, 'Reduzierung')) {return 'Reduzierung';}
        if (Str::contains($part->TEXT->DE, 'Schl??ssel')) {return optional($this->catLevel1['Werkzeug'])  ;}
        if (Str::contains($part->TEXT->DE, 'Schrumpf-Reduktion')) {return 'Schrumpf-Reduktion';}
        if (Str::contains($part->TEXT->DE, 'Spannfutter-Verl??ngerung')) {return 'Spannfutter-Verl??ngerung';}
        if (Str::contains($part->TEXT->DE, 'Spannkraftpr??fer')) {return 'Spannkraftpr??fer';}

        if (Str::contains($part->TEXT->DE, 'Werkzeugaufnahme')) {return 'Werkzeugaufnahme';}
        if (Str::contains($part->TEXT->DE, 'ThermoGrip??')) {return 'ThermoGrip??';}
        if (Str::contains($part->TEXT->DE, 'Spannschl??ssel')) {return optional($this->catLevel1['Werkzeug'])  ;}
        if (Str::contains($part->TEXT->DE, 'Rollenschl??sseleinsatz')) {return optional($this->catLevel1['Werkzeug'])  ;}
        if (Str::contains($part->TEXT->DE, 'Wkz.-Montageblock')) {return 'Wkz.-Montageblock';}
        if (Str::contains($part->TEXT->DE, 'Werkzeug-Montageblock')) {return optional($this->catLevel1['Werkzeug'])  ;}

        return 'Zubeh??r';
    }

    public function getCatLevel1($part)
    {
        if (Str::contains($part->TEXT->DE, '3-D Kantentaster')) {return 'Messmittel';}
        if (Str::contains($part->TEXT->DE, 'Anzugsbolzen')) {return 'Werkst??ckspannmittel';}
        if (Str::contains($part->TEXT->DE, 'Ferritscheibe')) {return 'Zubeh??r';}
        if (Str::contains($part->TEXT->DE, 'Hydrodehnspannf')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Spannmutter')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Schrumpffutter')) {return 'Zubeh??r';}
        if (Str::contains($part->TEXT->DE, 'Spannzangenfutter')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Spannzange')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Reduzierh??lse')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Weldon')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Wuchtmeister')) {return 'Messmittel';}
        if (Str::contains($part->TEXT->DE, 'Aufsteckfr??serdorn')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Dichtscheibe')) {return 'Werkst??ckspannmittel';}
        if (Str::contains($part->TEXT->DE, 'Drehmomentschl??ssel')) {return 'Werkezug';}
        if (Str::contains($part->TEXT->DE, 'Kombi-Aufsteckfd')) {return 'Werkst??ckspannmittel';}
        if (Str::contains($part->TEXT->DE, 'K??hlmittel??bergaberohr')) {return 'Zubeh??r';}
        if (Str::contains($part->TEXT->DE, 'NC-Bohrfutter')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Pr??fdorn')) {return 'Messmittel';}
        if (Str::contains($part->TEXT->DE, 'Rohling')) {return 'Zubeh??r';}
        if (Str::contains($part->TEXT->DE, 'Schnellwechseleinsatz')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Schrumpfspannzange')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Abdr??ckvorrichtung')) {return optional($this->catLevel1['Werkzeug'])  ;}
        if (Str::contains($part->TEXT->DE, 'Aufsteckdorn')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Blindstopfen')) {return 'Zubeh??r';}
        if (Str::contains($part->TEXT->DE, 'Dehnspannfutter')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'D??senring')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Fr??seranzugsschraube')) {return 'Zubeh??r';}
        if (Str::contains($part->TEXT->DE, 'Gewindeschneidfutter')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Gleitringspannmutter')) {return 'Zubeh??r';}
        if (Str::contains($part->TEXT->DE, 'Induktiv-Schrumpfger??t ')) {return 'Schrumpfger??te';}
        if (Str::contains($part->TEXT->DE, 'Kraftspannfutter')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}

        if (Str::contains($part->TEXT->DE, 'L??ngeneinstellh??lse')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Mitnehmerring')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Nutensteine')) {return 'Zubeh??r';}
        if (Str::contains($part->TEXT->DE, 'Reduzierung')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Schl??ssel')) {return optional($this->catLevel1['Werkzeug'])  ;}
        if (Str::contains($part->TEXT->DE, 'Schrumpf-Reduktion')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Spannfutter-Verl??ngerung')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Spannkraftpr??fer')) {return 'Messmittel';}

        if (Str::contains($part->TEXT->DE, 'Werkzeugaufnahme')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'ThermoGrip??')) {return $this->catLevel1['Werkzeugaufnahmen']   ? $this->catLevel1['Werkzeugaufnahmen'] : ''  ;}
        if (Str::contains($part->TEXT->DE, 'Spannschl??ssel')) {return optional($this->catLevel1['Werkzeug'])  ;}
        if (Str::contains($part->TEXT->DE, 'Rollenschl??sseleinsatz')) {return optional($this->catLevel1['Werkzeug'])  ;}
        if (Str::contains($part->TEXT->DE, 'Wkz.-Montageblock')) {return 'Wkz.-Montageblock';}
        if (Str::contains($part->TEXT->DE, 'Werkzeug-Montageblock')) {return optional($this->catLevel1['Werkzeug'])  ;}

        return 'Zubeh??r';
    }

    public function getCatLevel2($part)
    {
        if (Str::contains($part->TEXT->DE, '3-D Kantentaster')) {return '3-D Kantentaster';}
        if (Str::contains($part->TEXT->DE, 'Anzugsbolzen')) {return 'Anzugsbolzen';}
        if (Str::contains($part->TEXT->DE, 'Ferritscheibe')) {return 'Ferritscheibe';}
        if (Str::contains($part->TEXT->DE, 'Hydrodehnspannf')) {return 'Hydrodehnspannfutter';}
        if (Str::contains($part->TEXT->DE, 'Spannmutter')) {return 'Spannmutter';}
        if (Str::contains($part->TEXT->DE, 'Schrumpffutter')) {return 'Schrumpffutter';}
        if (Str::contains($part->TEXT->DE, 'Spannzangenfutter')) {return 'Spannzangenfutter';}
        if (Str::contains($part->TEXT->DE, 'Spannzange')) {return 'Spannzange';}
        if (Str::contains($part->TEXT->DE, 'Reduzierh??lse')) {return 'Reduzierh??lse';}
        if (Str::contains($part->TEXT->DE, 'Weldon')) {return 'Weldon';}
        if (Str::contains($part->TEXT->DE, 'Wuchtmeister')) {return 'Wuchtmeister';}
        if (Str::contains($part->TEXT->DE, 'Aufsteckfr??serdorn')) {return 'Aufsteckfr??serdorn';}
        if (Str::contains($part->TEXT->DE, 'Dichtscheibe')) {return 'Dichtscheibe';}
        if (Str::contains($part->TEXT->DE, 'Drehmomentschl??ssel')) {return 'Drehmomentschl??ssel';}
        if (Str::contains($part->TEXT->DE, 'Kombi-Aufsteckfd')) {return 'Kombi-Aufsteckfdorn';}
        if (Str::contains($part->TEXT->DE, 'K??hlmittel??bergaberohr')) {return 'K??hlmittel??bergaberohr';}
        if (Str::contains($part->TEXT->DE, 'NC-Bohrfutter')) {return 'NC-Bohrfutter';}
        if (Str::contains($part->TEXT->DE, 'Pr??fdorn')) {return 'Pr??fdorn';}
        if (Str::contains($part->TEXT->DE, 'Rohling')) {return 'Rohling';}
        if (Str::contains($part->TEXT->DE, 'Schnellwechseleinsatz')) {return 'Schnellwechseleinsatz';}
        if (Str::contains($part->TEXT->DE, 'Schrumpfspannzange')) {return 'Schrumpfspannzange';}
        if (Str::contains($part->TEXT->DE, 'Abdr??ckvorrichtung')) {return 'Abdr??ckvorrichtung';}
        if (Str::contains($part->TEXT->DE, 'Aufsteckdorn')) {return 'Aufsteckdorn';}
        if (Str::contains($part->TEXT->DE, 'Blindstopfen')) {return 'Blindstopfen';}
        if (Str::contains($part->TEXT->DE, 'Dehnspannfutter')) {return 'Dehnspannfutter';}
        if (Str::contains($part->TEXT->DE, 'D??senring')) {return 'D??senring';}
        if (Str::contains($part->TEXT->DE, 'Fr??seranzugsschraube')) {return 'Fr??seranzugsschraube';}
        if (Str::contains($part->TEXT->DE, 'Gewindeschneidfutter')) {return 'Gewindeschneidfutter';}
        if (Str::contains($part->TEXT->DE, 'Gleitringspannmutter')) {return 'Gleitringspannmutter';}
        if (Str::contains($part->TEXT->DE, 'Induktiv-Schrumpfger??t ')) {return 'Induktiv-Schrumpfger??t ';}
        if (Str::contains($part->TEXT->DE, 'Kraftspannfutter')) {return 'Kraftspannfutter';}

        if (Str::contains($part->TEXT->DE, 'L??ngeneinstellh??lse')) {return 'L??ngeneinstellh??lse';}
        if (Str::contains($part->TEXT->DE, 'Mitnehmerring')) {return 'Mitnehmerring';}
        if (Str::contains($part->TEXT->DE, 'Nutensteine')) {return 'Nutensteine';}
        if (Str::contains($part->TEXT->DE, 'Reduzierung')) {return 'Reduzierung';}
        if (Str::contains($part->TEXT->DE, 'Schl??ssel')) {return optional($this->catLevel1['Werkzeug'])  ;}
        if (Str::contains($part->TEXT->DE, 'Schrumpf-Reduktion')) {return 'Schrumpf-Reduktion';}
        if (Str::contains($part->TEXT->DE, 'Spannfutter-Verl??ngerung')) {return 'Spannfutter-Verl??ngerung';}
        if (Str::contains($part->TEXT->DE, 'Spannkraftpr??fer')) {return 'Spannkraftpr??fer';}

        if (Str::contains($part->TEXT->DE, 'Werkzeugaufnahme')) {return 'Werkzeugaufnahme';}
        if (Str::contains($part->TEXT->DE, 'ThermoGrip??')) {return 'ThermoGrip??';}
        if (Str::contains($part->TEXT->DE, 'Spannschl??ssel')) {return optional($this->catLevel1['Werkzeug'])  ;}
        if (Str::contains($part->TEXT->DE, 'Rollenschl??sseleinsatz')) {return optional($this->catLevel1['Werkzeug'])  ;}
        if (Str::contains($part->TEXT->DE, 'Wkz.-Montageblock')) {return 'Wkz.-Montageblock';}
        if (Str::contains($part->TEXT->DE, 'Werkzeug-Montageblock')) {return optional($this->catLevel1['Werkzeug'])  ;}

        return 'Zubeh??r';
    }

}
