<?php

namespace App\Producers\Dixi\ImportScripts;

use App\Producers\Dixi\Models\DixiPartsDe;
use App\Producers\Dixi\Models\DixiPartsEn;
use App\Producers\Dixi\Models\DixiPartsFr;
use App\Producers\Dixi\Models\DixiPartsIt;

class DixiMapComparableData
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
            case 'it':
                $this->importItalianData();
                break;
        }
    }

    public function importGermanData()
    {
        foreach (DixiPartsDe::all() as $part) {

            $part->update($this->getAttributes($part));
        }
    }

    public function importEnglishData()
    {
        foreach (DixiPartsEn::all() as $part) {

            $part->update($this->getAttributes($part));
        }
    }

    public function importFrenchData()
    {
        foreach (DixiPartsFr::all() as $part) {

            $part->update($this->getAttributes($part));
        }
    }

    public function importItalianData()
    {
        foreach (DixiPartsIt::all() as $part) {

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
            'cutting_edge_diameter'             =>  array_key_exists('D1', $values) ? $values['D1'].' mm' : null,
            'cutting_edge_diameter_value'       =>  array_key_exists('D1', $values) ? $values['D1'] : null,
            'overall_length'                    =>  array_key_exists('L', $values) ? $values['L'].' mm' : null,
            'overall_length_value'              =>  array_key_exists('L', $values) ? $values['L'] : null,
            'flute_length'                      =>  array_key_exists('L1', $values) ? $values['L1'].' mm' : null,
            'flute_length_value'                =>  array_key_exists('L1', $values) ? $values['L1'] : null,
            'shank_diameter'                    =>  array_key_exists('D', $values) ? $values['D'].' mm' : null,
            'shank_diameter_value'              =>  array_key_exists('D', $values) ? $values['D'] : null,
            'number_of_cutting_edges'           =>  array_key_exists('Z', $values) ? $values['Z'] : null,
            'pitch'                             =>  array_key_exists('P', $values) ? $values['P'].' mm' : null,
            'pitch_value'                       =>  array_key_exists('P', $values) ? $values['P'] : null,
            'tolerance_nominal'                 =>  array_key_exists('T nom', $values) ? $values['T nom'] : null,
            'diameter'                          =>  array_key_exists('D1', $values) ? $values['D1'].' mm' : null,
            'diameter_value'                    =>  array_key_exists('D1', $values) ? $values['D1'] : null,
        ];
    }
}
