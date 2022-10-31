<?php

namespace App\Producers\Gloor\ImportScripts;

use App\Producers\Gloor\Models\GloorPartsDe;
use App\Producers\Gloor\Models\GloorPartsEn;
use App\Producers\Gloor\Models\GloorPartsFr;
use App\Producers\Gloor\Models\GloorPartsIt;

class GloorMapComparableData
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
        foreach (GloorPartsDe::all() as $part) {

            $part->update($this->getAttributes($part));
        }
    }

    public function importEnglishData()
    {
        foreach (GloorPartsEn::all() as $part) {

            $part->update($this->getAttributes($part));
        }
    }

    public function importFrenchData()
    {
        foreach (GloorPartsFr::all() as $part) {

            $part->update($this->getAttributes($part));
        }
    }

    public function importItalianData()
    {
        foreach (GloorPartsIt::all() as $part) {

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
            'cutting_edge_diameter'             =>  $this->getCuttingEdgeDiameter($values) ? $this->getCuttingEdgeDiameter($values).' mm' : null,
            'cutting_edge_diameter_value'       =>  $this->getCuttingEdgeDiameter($values),
            'bore_diameter'                     =>  $this->getBoreDiameter($values) ? $this->getBoreDiameter($values).' mm' : null,
            'bore_diameter_value'               =>  $this->getBoreDiameter($values),
            'overall_length'                    =>  array_key_exists('l1', $values) ? $values['l1'].' mm' : null,
            'overall_length_value'              =>  array_key_exists('l1', $values) ? $values['l1'] : null,
            'shank_diameter'                    =>  array_key_exists('d2', $values) ? $values['d2'].' mm' : null,
            'shank_diameter_value'              =>  array_key_exists('d2', $values) ? $values['d2'] : null,
            'number_of_cutting_edges'           =>  array_key_exists('z', $values) ? $values['z'] : null,
            'pitch'                             =>  $this->getPitch($values) ? $this->getPitch($values).' mm' : null,
            'pitch_value'                       =>  $this->getPitch($values) ,
            'thread'                            =>  $this->getThread($part, $values),
            'thickness_of_cutting_edge'         =>  $this->getThicknessOfCuttingEdge($values) ? $this->getThicknessOfCuttingEdge($values).' mm' : null,
            'thickness_of_cutting_edge_value'   =>  $this->getThicknessOfCuttingEdge($values)
        ];
    }

    public function getThread($part, $values) {
        if($part->thread) {
            return $part->thread;
        }
        if(array_key_exists('m', $values) ) {
            return 'M '.$values['m'] ;
        }
        return null;
    }

    public function getBoreDiameter($values) {
        if(array_key_exists('d', $values) ) {
            return $values['d'];
        }
        if(array_key_exists('d2', $values) ) {
            return $values['d2'];
        }
        return null;
    }

    public function getCuttingEdgeDiameter($values) {
        if(array_key_exists('d1', $values) ) {
            return $values['d1'];
        }
        if(array_key_exists('D', $values) ) {
            return $values['D'];
        }
        return null;
    }

    public function getThicknessOfCuttingEdge($values) {
        if(array_key_exists('B', $values) ) {
            return $values['B'];
        }
        if(array_key_exists('b1', $values) ) {
            return $values['b1'];
        }
        return null;
    }

    public function getPitch($values) {
        if(array_key_exists('p', $values) ) {
            return $values['p'];
        }
        if(array_key_exists('t', $values) ) {
            return $values['t'];
        }
        return null;
    }
}
