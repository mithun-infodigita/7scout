<?php
namespace App\Producers\Voelkel\ImportScripts;

use App\Models\CustomsSetting\CustomsSetting;
use App\Producers\Voelkel\Models\VoelkelPartsDe;
use App\Producers\Voelkel\Models\VoelkelPartsEn;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;


class VoelkelBasicImport implements ToCollection, WithHeadingRow
{
    public $import;

    public $terms;

    public $texts;

    public $praefData;

    public $images;

    public $customsSettings;

    public $previousValue;


    public function basicImport($import)
    {
        $this->import = $import;

        $this->customsSettings = CustomsSetting::all();

        $textImport = new VoelkelTextImport();

        $this->terms = $textImport->getTerms($import);

        $this->texts = $textImport->getTexts($import);

        $praefDataImport = new VoelkelPraefDataImport();

        $this->praefData = $praefDataImport->getPraefData($import);

        $imageImport = new VoelkelImageImport();

        $this->images = $imageImport->getImagess($import);

        HeadingRowFormatter::extend('slug', function($value, $key) {
            if($value === 'mm/Zoll') {
                return Str::slug($this->previousValue, '_').'_'.Str::slug($value, '_');
            }

            $this->previousValue = $value;

            return Str::slug($value, '_');
        });

        $files = $import->getMedia();

        foreach ($files as $file) {
            if (file_exists($file->getPath())) {

                Excel::import($this, $file->getPath());
            }
        }
    }

    public function collection(Collection $rows)
    {
        $this->importParts($rows);
    }

    public function importParts($rows)
    {
        foreach ($rows as $row) {
            switch ($this->import->language) {
                case 'de':
                    $this->importPartsDe($row);
                    break;
                case 'en':
                    $this->importPartsEn($row);
                    break;
            }
        }
    }

    public function importPartsDe($row)
    {

        $row = $row->toArray();

        $partId = $this->import->producer->unique_id . '_' . $row['art_nr'];
        $attributes = $this->getAttributesDe($row);


        if($row['art_nr']) {
            VoelkelPartsDe::updateOrCreate(
                ['part_id' => $partId],
                $attributes
            );
        }
    }

    public function importPartsEn($row)
    {

        $row = $row->toArray();


        $partId = $this->import->producer->unique_id . '_' . $row['art_nr'];

        $attributes = $this->getAttributesEn($row);

        if($row['art_nr']) {
            VoelkelPartsEn::updateOrCreate(
                ['part_id' => $partId],
                $attributes
            );
        }
    }


    public function getAttributesDe($row)
    {

        $attributes = [
            'producer_id'                   =>  'voelkel',
            'producer_name'                 =>  'Völkel GmbH',
            'part_number'                   =>  $row['art_nr'],
            'part_name'                     =>  $this->getPartNameDe($row),
            'country_of_origin'             =>  'DE',
            'stock'                         =>  json_encode([ "9" =>  10]),
            'reprocurement_time'            =>  21,
            'weight'                        =>  floatval($row['gewicht_kg']),
            'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
            'preferential_beneficiary_eu'   =>  $this->getPraef($row),
            'part_specifications'           =>  $this->getPartSpecifications($row),
            'application'                   =>  $this->getApplicationAttributeDe($row),
            'speciality'                    =>  $this->getSpecialityAttributeDe($row),
            'thread_type'                   =>  array_key_exists('gewindeart', $row) ? $row['gewindeart'] : null,
            'standard'                      =>  array_key_exists('norm_baumasse', $row) ? $row['norm_baumasse'] : null,
            'thread_standard'               =>  array_key_exists('gewindenorm', $row) ? $row['gewindenorm'] : null,
            'cutting_material'              =>  array_key_exists('material', $row) ? $row['material'] : null,
            'coating'                       =>  array_key_exists('beschichtung', $row) ? $row['beschichtung'] : null,
            'cutting_edge_diameter'         =>  array_key_exists('nennmass_d', $row) ? str_replace('"', '',$row['nennmass_d'] ) . ' ' .  $row['nennmass_d_mmzoll']: null,
            'cutting_edge_diameter_value'   =>  array_key_exists('nennmass_d', $row) ? str_replace('"', '',$row['nennmass_d'] ): null,
            'diameter'                      =>  array_key_exists('nennmass_d', $row) ? str_replace('"', '',$row['nennmass_d'] ) . ' ' .  $row['nennmass_d_mmzoll']: null,
            'diameter_value'                =>  array_key_exists('nennmass_d', $row) ? str_replace('"', '',$row['nennmass_d'] ): null,
            'pitch'                         =>  array_key_exists('steigung_p', $row) ? $row['steigung_p'] . ' ' . $row['mmtpi']: null,
            'pitch_value'                   =>  array_key_exists('steigung_p', $row) ? $row['steigung_p']: null,
            'shank_diameter'                =>  array_key_exists('d2', $row) ? $row['d2']. 'mm': null,
            'shank_diameter_value'          =>  array_key_exists('d2', $row) ? $row['d2'] : null,
            'type_of_flute'                 =>  array_key_exists('nuten_form', $row) ? $row['nuten_form']: null,
            'core_hole_diameter'            =>  array_key_exists('kernlochdurchmesser', $row) ? $row['kernlochdurchmesser'].' mm': null,
            'core_hole_diameter_value'      =>  array_key_exists('kernlochdurchmesser', $row) ? $row['kernlochdurchmesser']: null,
            'mapping_helper'                =>  $this->getMappingHelper($row),
            'price'                         =>  $this->getPrice($row),
            'image_links'                   =>   json_encode($this->getImageLinks($row)),
            'table_detail_image_link'       =>  $this->getTableDetailImageLink($row),
            'video_url'                     =>  $this->getVideoUrl($row),
            'hole_type'                     =>  $this->getHoleTypeDe($row),
            'part_description'              =>  $this->getPartDescriptionDe($row),
            'sort_value'                    =>  array_key_exists('nennmass_d', $row) ? str_replace('"', '',$row['nennmass_d'] ): null,
            'thread_type_description'       =>  $this->getThreadTypeDescriptionDe($row),
            'thread'                        =>  $this->getThread($row)
        ];

        return $attributes;
    }

    public function getThreadDe($row)
    {

        if (array_key_exists('gewindeart', $row) && array_key_exists('nennmass_d', $row)  && array_key_exists('nennmass_d_mmzoll', $row) ) {
            return $row['gewindeart'] .' '. $row['nennmass_d'] . ' '. $row['nennmass_d_mmzoll'];
        }
    }

    public function getAttributesEn($row)
    {
        $attributes = [
            'producer_id'                   =>  'voelkel',
            'producer_name'                 =>  'Völkel GmbH',
            'part_number'                   =>  $row['art_nr'],
            'part_name'                     =>  $this->getPartNameEn($row),
            'country_of_origin'             =>  'DE',
            'stock'                         =>  json_encode([ "9" =>  10]),
            'reprocurement_time'            =>  21,
            'weight'                        =>  floatval($row['gewicht_kg']),
            'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
            'preferential_beneficiary_eu'   =>  $this->getPraef($row),
            'part_specifications'           =>  $this->getPartSpecifications($row),
            'application'                   =>  $this->getApplicationAttributeEn($row),
            'speciality'                    =>  $this->getSpecialityAttributeEn($row),
            'thread_type'                   =>  array_key_exists('gewindeart', $row) ? $row['gewindeart'] : null,
            'standard'                      =>  array_key_exists('norm_baumasse', $row) ? $row['norm_baumasse'] : null,
            'thread_standard'               =>  array_key_exists('gewindenorm', $row) ? $row['gewindenorm'] : null,
            'cutting_material'              =>  array_key_exists('material', $row) ? $row['material'] : null,
            'coating'                       =>  array_key_exists('beschichtung', $row) ? $row['beschichtung'] : null,
            'cutting_edge_diameter'         =>  array_key_exists('nennmass_d', $row) ? str_replace('"', '',$row['nennmass_d'] ) . ' ' .  $row['nennmass_d_mmzoll']: null,
            'cutting_edge_diameter_value'   =>  array_key_exists('nennmass_d', $row) ? str_replace('"', '',$row['nennmass_d'] ): null,
            'diameter'                      =>  array_key_exists('nennmass_d', $row) ? str_replace('"', '',$row['nennmass_d'] ) . ' ' .  $row['nennmass_d_mmzoll']: null,
            'diameter_value'                =>  array_key_exists('nennmass_d', $row) ? str_replace('"', '',$row['nennmass_d'] ): null,
            'pitch'                         =>  array_key_exists('steigung_p', $row) ? $row['steigung_p'] . ' ' . $row['mmtpi']: null,
            'pitch_value'                   =>  array_key_exists('steigung_p', $row) ? $row['steigung_p']: null,
            'shank_diameter'                =>  array_key_exists('d2', $row) ? $row['d2']. 'mm': null,
            'shank_diameter_value'          =>  array_key_exists('d2', $row) ? $row['d2'] : null,
            'type_of_flute'                 =>  array_key_exists('nuten_form', $row) ? $row['nuten_form']: null,
            'core_hole_diameter'            =>  array_key_exists('kernlochdurchmesser', $row) ? $row['kernlochdurchmesser'].' mm': null,
            'core_hole_diameter_value'      =>  array_key_exists('kernlochdurchmesser', $row) ? $row['kernlochdurchmesser']: null,
            'mapping_helper'                =>  $this->getMappingHelper($row),
            'price'                         =>  $this->getPrice($row),
            'image_links'                   =>   json_encode($this->getImageLinks($row)),
            'table_detail_image_link'       =>  $this->getTableDetailImageLink($row),
            'video_url'                     =>  $this->getVideoUrl($row),
            'hole_type'                     =>  $this->getHoleTypeEn($row),
            'part_description'              =>  $this->getPartDescriptionEn($row),
            'sort_value'                    =>  array_key_exists('nennmass_d', $row) ? str_replace('"', '',$row['nennmass_d'] ): null,
            'thread_type_description'       =>  $this->getThreadTypeDescriptionEn($row),
            'thread'                        =>  $this->getThread($row)
        ];

        return $attributes;
    }


    public function getThread($row)
    {
        if (array_key_exists('gewindeart', $row) && array_key_exists('nennmass_d', $row)   ) {
            return $row['gewindeart'] .' '. $row['nennmass_d'] ;
        }
    }


    public function getThreadTypeDescriptionDe($row)
    {
        if(array_key_exists('gewindeart', $row)) {

            if($this->terms->where('kurzbez', $row['gewindeart'])->first() &&  array_key_exists('bezeichnung_deutsch', $this->terms->where('kurzbez', $row['gewindeart'])->first() )) {
                return $this->terms->where('kurzbez', $row['gewindeart'])->first()['bezeichnung_deutsch'];
            }
        }
    }

    public function getThreadTypeDescriptionEn($row)
    {
        if(array_key_exists('gewindeart', $row)) {

            if($this->terms->where('kurzbez', $row['gewindeart'])->first() &&  array_key_exists('bezeichnung_englisch', $this->terms->where('kurzbez', $row['gewindeart'])->first() )) {
                return $this->terms->where('kurzbez', $row['gewindeart'])->first()['bezeichnung_englisch'];
            }
        }
    }

    public function getPrice($row)
    {
        return json_encode([
            'currency'      =>  'EUR',
            'value'         =>  $row['eur_listenpreis_2022']
        ]);
    }

    public function getMappingHelper($row)
    {
        $mappingHelper = '';
        $mappingHelper .= array_key_exists('art', $row) ? $row['art'].'|' : '';
        $mappingHelper .= array_key_exists('gewindeart', $row) ? $row['gewindeart'].'|' : '';
        $mappingHelper .= array_key_exists('rhlh', $row) ? $row['rhlh'].'|' : '';
        $mappingHelper .= array_key_exists('bohrung', $row) ? $row['bohrung'].'|' : '';
        $mappingHelper .= array_key_exists('typ', $row) ? $row['typ'].'|' : '';

        return $mappingHelper;
    }

    public function getPartNameDe($row)
    {
        $partName = '';
        if(array_key_exists('art', $row) && $this->terms->where('kurzbez', $row['art'])->first()) {
            $partName .= $this->terms->where('kurzbez', $row['art'])->first()['bezeichnung_deutsch'];
        }
        elseif (array_key_exists('typ', $row) && $this->terms->where('kurzbez', $row['typ'])->first()) {
            $partName .= $this->terms->where('kurzbez', $row['typ'])->first()['bezeichnung_deutsch'];
        }

        if(array_key_exists('gewindeart', $row)) {
            $partName .= ' '.$row['gewindeart'];
        }

        if(array_key_exists('nennmass_d', $row)) {
            $partName .= ' '.$row['nennmass_d'];
        }

        return $partName;
    }

    public function getPartNameEn($row)
    {
        $partName = '';
        if(array_key_exists('art', $row) && $this->terms->where('kurzbez', $row['art'])->first()) {
            $partName .= $this->terms->where('kurzbez', $row['art'])->first()['bezeichnung_englisch'];
        }
        elseif (array_key_exists('typ', $row) && $this->terms->where('kurzbez', $row['typ'])->first()) {
            $partName .= $this->terms->where('kurzbez', $row['typ'])->first()['bezeichnung_englisch'];
        }

        if(array_key_exists('gewindeart', $row)) {
            $partName .= ' '.$row['gewindeart'];
        }

        if(array_key_exists('nennmass_d', $row)) {
            $partName .= ' '.$row['nennmass_d'];
        }

        return $partName;
    }

    public function getPraef($row)
    {
        if($this->praefData->where('art_no', $row['art_nr'])->first()  ) {
            return $this->praefData->where('art_no', $row['art_nr'])->first()['origin']  === 'EU' ;
        }

        return 0;
    }

    public function getHoleTypeDe($row)
    {
        if(array_key_exists('bohrung', $row)) {
            if($row['bohrung'] === 'DULO') {
                return 'Durchgangsloch';
            }
            if($row['bohrung'] === 'SALO') {
                return 'Sackloch';
            }
            if($row['bohrung'] === 'DSLO') {
                return 'Durchgangs-/Sackloch';
            }
        }
    }

    public function getHoleTypeEn($row)
    {
        if(array_key_exists('bohrung', $row)) {
            if($row['bohrung'] === 'DULO') {
                return 'Through hole';
            }
            if($row['bohrung'] === 'SALO') {
                return 'Blind hole';
            }
            if($row['bohrung'] === 'DSLO') {
                return 'Through/Blind hole';
            }
        }
    }

    public function getApplicationAttributeDe($row)
    {
        if(array_key_exists('typ', $row)) {

            if($this->terms->where('kurzbez', $row['typ'])->first() &&  array_key_exists('bezeichnung_deutsch', $this->terms->where('kurzbez', $row['typ'])->first() )) {
                return $this->terms->where('kurzbez', $row['typ'])->first()['bezeichnung_deutsch'];
            }
        }
    }

    public function getApplicationAttributeEn($row)
    {
        if(array_key_exists('typ', $row)) {

            if($this->terms->where('kurzbez', $row['typ'])->first() &&  array_key_exists('bezeichnung_english', $this->terms->where('kurzbez', $row['typ'])->first() )) {
                return $this->terms->where('kurzbez', $row['typ'])->first()['bezeichnung_english'];
            }
        }
    }

    public function getSpecialityAttributeDe($row)
    {
        if(array_key_exists('besonderheit', $row)) {

            if($this->terms->where('kurzbez', $row['besonderheit'])->first() &&  array_key_exists('bezeichnung_deutsch', $this->terms->where('kurzbez', $row['besonderheit'])->first() )) {
                return $this->terms->where('kurzbez', $row['besonderheit'])->first()['bezeichnung_deutsch'];
            }
        }
    }

    public function getSpecialityAttributeEn($row)
    {
        if(array_key_exists('besonderheit', $row)) {

            if($this->terms->where('kurzbez', $row['besonderheit'])->first() &&  array_key_exists('bezeichnung_english', $this->terms->where('kurzbez', $row['besonderheit'])->first() )) {
                return $this->terms->where('kurzbez', $row['besonderheit'])->first()['bezeichnung_english'];
            }
        }
    }


    public function getPartSpecifications($row)
    {
        $partSpecifications = [];

        $partSpecifications['l1'] =   array_key_exists('l1', $row) ? $row['l1'].' mm' : null;
        $partSpecifications['l2'] =   array_key_exists('l2', $row) ? $row['l2'].' mm' : null;

        return json_encode(array_filter($partSpecifications));
    }

    public function getTableDetailImageLink($row)
    {
        if(array_key_exists('bilddaten_nr', $row)) {

            if($this->images->where('bilddaten_nr', $row['bilddaten_nr'])->first() &&  array_key_exists('bilddaten_nr', $this->images->where('bilddaten_nr', $row['bilddaten_nr'])->first() )) {
                if(!empty($this->images->where('bilddaten_nr', $row['bilddaten_nr'])->first()['image1'])) {
                    $link = $this->images->where('bilddaten_nr', $row['bilddaten_nr'])->first()['image1'];
                    $link = str_replace('tif', 'jpg', $link);
                    return env('APP_URL') . '/storage/producers/voelkel/partImages/' . $link;

                }
            }
        }
    }

    public function getImageLinks($row)
    {
        $imageLinks = [];
        if(array_key_exists('bilddaten_nr', $row)) {

            if($this->images->where('bilddaten_nr', $row['bilddaten_nr'])->first() &&  array_key_exists('bilddaten_nr', $this->images->where('bilddaten_nr', $row['bilddaten_nr'])->first() )) {

                for ($i = 1; $i <= 14; $i++) {
                    if(!empty($this->images->where('bilddaten_nr', $row['bilddaten_nr'])->first()['image'.$i])) {
                        $link = $this->images->where('bilddaten_nr', $row['bilddaten_nr'])->first()['image'.$i];
                        $link = str_replace('tif', 'jpg', $link);
                        array_push($imageLinks,env('APP_URL') . '/storage/producers/voelkel/partImages/' . $link);

                    }
                }

            }
        }
        return $imageLinks;
    }

    public function getVideoUrl($row)
    {
        if(array_key_exists('bilddaten_nr', $row)) {


            if($this->images->where('bilddaten_nr', $row['bilddaten_nr'])->first() &&  array_key_exists('bilddaten_nr', $this->images->where('bilddaten_nr', $row['bilddaten_nr'])->first() )) {
                if(!empty($this->images->where('bilddaten_nr', $row['bilddaten_nr'])->first()['verlinkung_2'])) {
                    return $this->images->where('bilddaten_nr', $row['bilddaten_nr'])->first()['verlinkung_2'];
                }
            }
        }
    }

    public function getPartDescriptionDe($row)
    {

        if(array_key_exists('anwendung_ta', $row)) {

            if($this->texts->where('text_nr', $row['anwendung_ta'])->first() &&  array_key_exists('text_deutsch', $this->texts->where('text_nr', $row['anwendung_ta'])->first() )) {
                return $this->texts->where('text_nr', $row['anwendung_ta'])->first()['text_deutsch'];
            }
        }
    }

    public function getPartDescriptionEn($row)
    {

        if(array_key_exists('anwendung_ta', $row)) {

            if($this->texts->where('text_nr', $row['anwendung_ta'])->first() &&  array_key_exists('text_englisch', $this->texts->where('text_nr', $row['anwendung_ta'])->first() )) {
                return $this->texts->where('text_nr', $row['anwendung_ta'])->first()['text_englisch'];
            }
        }
    }

    public function getCustomsTariffNumbers($row)
    {

        $customSetting = $this->customsSettings->where('customs_tariff_number_de',  $row['zolltarifnummer'])->first();

        return [
            'EU'    =>   $row['zolltarifnummer'],
            'CH'    =>  $customSetting ? $customSetting->customs_tariff_number_ch : null,
        ];
    }
}
