<?php

namespace App\Producers\Amf\ImportScripts;

use App\Producers\Amf\Models\AmfPartsDe;

class AmfUpdateMaterialGroupId
{
    public $import;

    public function updateMaterialGroupId($import)
    {
        $this->import = $import;

        $files = $import->getMedia();

        foreach ($files as $file) {
            if (file_exists($file->getPath())) {
                $this->importParts($file);
            }
        }
    }

    public function importParts($file)
    {
        $xml = simplexml_load_file($file->getPath());

        $partGroupMapping = $this->getPartGroupMapping($xml);

        $materialGroupStructure = $this->getMaterialGroupStructure($xml);

        $this->importGermanData($partGroupMapping, $materialGroupStructure );

    }

    public function importGermanData($partGroupMapping, $materialGroupStructure)
    {

        foreach (AmfPartsDe::all() as $part){
            $part->update([
                'material_group'                        =>  $partGroupMapping[$part->part_number],
                'producer_material_group_structure'     =>  $this->getPartGroupStructure($part, $materialGroupStructure)
            ]);
        }

    }

    public function getPartGroupStructure($part, $materialGroupStructure)
    {
        $parentExists = true;

        $string = $part->material_group;;
        $materialGroup = $part->material_group;
        while ($parentExists) {
            if(array_key_exists($materialGroup, $materialGroupStructure)) {
                $string = $materialGroupStructure[$materialGroup]['group_name'].'|'.$string;
                $materialGroup = $materialGroupStructure[$materialGroup]['parent_id'];
            }
            else {
                $parentExists = false;
            }
        }


        return $string;
    }

    public function getPartGroupMapping($xml)
    {
        $mappings = [];

        foreach ($xml->T_NEW_CATALOG->ARTICLE_TO_CATALOGGROUP_MAP as $mapping) {

            $mappings[(string)$mapping->ART_ID] = (string)$mapping->CATALOG_GROUP_ID;
        }

        return $mappings;
    }

    public function getMaterialGroupStructure($xml)
    {
        $items = [];

        foreach ($xml->T_NEW_CATALOG->CATALOG_GROUP_SYSTEM->CATALOG_STRUCTURE as $item) {

            $items[(string)$item->GROUP_ID] = [
                'group_name'    => (string)$item->GROUP_NAME,
                'parent_id'     =>  (string)$item->PARENT_ID,
            ];
        }

        return $items;
    }

}
