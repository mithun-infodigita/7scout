<?php

namespace App\Producers\Diebold\ImportScripts;

use App\Producers\Diebold\Models\DieboldPartsDe;
use App\Producers\Diebold\Models\DieboldPartsEn;
use App\Producers\Diebold\Models\DieboldPartsFr;

class DieboldMapComparableData
{
    public $import;

    public function mapData($import)
    {
        $this->import = $import;

        $this->mapComparableData();
    }

    public function mapComparableData() {
        switch ($this->import->language) {
            case 'de':
                $this->importGermanData();
                break;
            case 'en':
                $this->importEnglishData();
                break;
            case 'fr':
                $this->importFrenchData();
                break;
        }
    }

    public function importGermanData()
    {
        foreach (DieboldPartsDe::all() as $part) {

            $part->update($this->getAttributes($part));
        }
    }

    public function importEnglishData()
    {
        foreach (DieboldPartsEn::all() as $part) {

            $part->update($this->getAttributes($part));
        }
    }

    public function importFrenchData()
    {
        foreach (DieboldPartsFr::all() as $part) {

            $part->update($this->getAttributes($part));
        }
    }


    public function getAttributes($part)
    {
        $values = json_decode($part->part_specification_values, true);

        if(!count($values)) {
            return [];
        }

        return [
            'diameter'             =>  array_key_exists('d1', $values) ? $values['d1'].' mm' : null,
            'length'               =>  array_key_exists('l1', $values) ? $values['l1'].' mm' : null,
        ];
    }
}
