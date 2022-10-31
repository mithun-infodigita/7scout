<?php

namespace App\Producers\Diebold\ImportScripts;

use App\Http\Controllers\Api\Translation\TranslationController;
use App\Models\CustomsSetting\CustomsSetting;
use App\Producers\Diebold\Models\DieboldPartsDe;
use App\Producers\Diebold\Models\DieboldPartsEn;
use App\Producers\Diebold\Models\DieboldPartsFr;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Str;

class DieboldBasicImport
{
    public $import;



    public function basicImport($import)
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

        $item = [];

        foreach ($xml->FAMILIE as $family) {

            $item['familyId'] = (string)$family->attributes()->ID;
            foreach ($family->GRUPPE as $group) {

                $item['groupId'] = (string)$group->attributes()->ID;

                $item['imageAndDrawingLinks'] = $this->getImageAndDrawingLinks($group);

                $groupTexts = $this->getGroupTexts($group);

                $item['description_de'] = array_key_exists('description_de', $groupTexts) ? $groupTexts['description_de'] : null;

                $item['description_en'] = array_key_exists('description_en', $groupTexts) ? $groupTexts['description_en'] : null;

                foreach ($group->ARTIKEL as $part) {

                    $item['partId'] = (string)$part->attributes()->ID;

                    $item['features']   =   $this->getFeatures($part);


                    switch ($this->import->language) {
                        case 'de':
                            $this->importPartsDe($part, $item);
                            break;
                        case 'en':
                            $this->importPartsEn($part, $item);
                            break;
                        case 'fr':
                            $this->importPartsFr($part, $item);
                            break;
                    }
                }
            }
        }
    }

    public function importPartsDe($part, $item)
    {
        $partId = $this->import->producer->unique_id . '_' . str_replace('.', '', $item['partId']);

        $texts = $this->getTexts($part);

        $attributes = $this->getAttributes($part, $item);

        $attributes['part_name'] =  $texts['part_name_de'];

        $attributes['part_description'] =  $this->parseGroupTexts($item['description_de']) ;

        DieboldPartsDe::updateOrCreate(
            ['part_id' => $partId],
            $attributes
        );
    }

    public function importPartsEn($part, $item)
    {
        $partId = $this->import->producer->unique_id . '_' . str_replace('.', '', $item['partId']);

        $texts = $this->getTexts($part);

        $attributes = $this->getAttributes($part, $item);

        $attributes['part_name'] =  $texts['part_name_en'];

        $attributes['part_description'] =  $this->parseGroupTexts($item['description_en']) ;

        DieboldPartsEn::updateOrCreate(
            ['part_id' => $partId],
            $attributes
        );
    }

    public function importPartsFr($part, $item)
    {
        $partId = $this->import->producer->unique_id . '_' . $item['partId'];

        $texts = $this->getTexts($part);

        $attributes = $this->getAttributes($part, $item);

        $translation = new TranslationController();

        $attributes['part_name'] =  $translation->translate($texts['part_name_de'], 'de', 'fr');

        if(array_key_exists('description_de', $item) && $item['description_de'] ) {

            $attributes['part_description'] =  $this->getPartDescriptionTranslation($this->parseGroupTexts($item['description_de']), 'fr');
        }

        DieboldPartsFr::updateOrCreate(
            ['part_id' => $partId],
            $attributes
        );
    }

    public function getPartDescriptionTranslation($text, $language) {
        $attributes = json_decode($text);

        $translatedAttributes = [];

        $translation = new TranslationController();

        foreach ($attributes as $key => $value) {
            $translatedAttributes[$translation->translate($key , 'de', $language)] = html_entity_decode($translation->translate($value , 'de', $language)) ;
        }

        return json_encode($translatedAttributes);
    }

    public function getTexts($part)
    {
        $texts = [];

        foreach ($part->TEXT as $text) {

            if ((string)$text->attributes()->Typ === 'ArtBez') {
                $texts['part_name_de'] = (string)$text->DE;
                $texts['part_name_en'] = (string)$text->EN;
            }
        }

        return $texts;
    }

    public function getGroupTexts($group)
    {
        $texts = [];

        foreach ($group->TEXT as $text) {
            if ((string)$text->attributes()->Typ === 'InfoB') {
                $texts['description_de'] = (string)$text->DE;
                $texts['description_en']  = (string)$text->EN;
            }
        }

        return $texts;
    }

    public function parseGroupTexts($text)
    {
        $partDescription = [];

        if(!$text) {
            return;
        }

        $dom = new DOMDocument;
        $dom->loadHTML($text);
        $xpath = new DOMXPath($dom);

        foreach ($xpath->query('//tbody/tr') as $tr) {
            $i = 0;
            $value = [];
            foreach ($xpath->query("td", $tr) as $td) {
                $value[$i] = trim(stripcslashes(strip_tags(preg_replace('/\s+/S', " ", $td->nodeValue))));

                $i++;
            }

            $partDescription[rtrim($value[0], ':')] = $value[1];
        }

        $textWithoutTable = trim(stripcslashes(strip_tags(preg_replace('/\s+/S', " ", preg_replace('#<table[^>]*>.*?</table>#si', '', $text)), '')));

        $textWithoutTable = str_replace('&bull;', "<br /> &bull; ", $textWithoutTable);

        if(!empty($textWithoutTable) && $textWithoutTable != '&nbsp;') {

            $partDescription['Beschreibung'] = $textWithoutTable;
        }

        if (count($partDescription)) {
            return preg_replace("/\r|\n/", "", json_encode($partDescription));
        }

        return;

    }

    public function getAttributes($part, $item)
    {
        $attributes = [
            'producer_id'                   =>  'diebold',
            'producer_name'                 =>  'Diebold',
            'part_number'                   =>   $item['partId'],
            'part_specifications'           =>   $this->getPartSpecifications($part, $item),
            'part_specification_values'     =>   $this->getPartSpecificationValues($item),
            'image_links'                   =>   implode(",", $item['imageAndDrawingLinks']['image_links']),
            'drawing_link'                  =>   array_key_exists('drawing_link', $item['imageAndDrawingLinks']) ? $item['imageAndDrawingLinks']['drawing_link'] : null,
            'table_detail_image_link'       =>   $item['imageAndDrawingLinks']['table_detail_image_link'],
            'stock_part_id'                 =>   $item['partId'],
            'reprocurement_time'            =>  21,
            'stock'                         =>  json_encode([ "4" =>  0]),
        ];

        return $attributes;
    }

    public function getPartSpecifications($part, $item)
    {
        $partSpecifications = [];

        $partSpecifications['d1']   = array_key_exists('d1', $item['features']) ? str_replace(",",".",$item['features']['d1'] ).' mm': null;
        $partSpecifications['d2']   = array_key_exists('d2', $item['features']) ? str_replace(",",".",$item['features']['d2'] ).' mm': null;
        $partSpecifications['d3']   = array_key_exists('d3', $item['features']) ? str_replace(",",".",$item['features']['d3'] ).' mm': null;
        $partSpecifications['l1']   = array_key_exists('l1', $item['features']) ? str_replace(",",".",$item['features']['l1'] ).' mm': null;
        $partSpecifications['l2']   = array_key_exists('l2', $item['features']) ? str_replace(",",".",$item['features']['l2'] ).' mm': null;
        $partSpecifications['l3']   = array_key_exists('l3', $item['features']) ? str_replace(",",".",$item['features']['l3'] ).' mm': null;
        $partSpecifications['A']    = array_key_exists('A', $item['features']) ? str_replace(",",".",$item['features']['A'] ).' mm': null;
        $partSpecifications['g']    = array_key_exists('g', $item['features']) ? str_replace(",",".",$item['features']['g'] ): null;

        $partSpecifications =   array_filter($partSpecifications, function ($item) {
            return $item != null;
        });

        return json_encode($partSpecifications);
    }

    public function getPartSpecificationValues($item)
    {
        $partSpecificationValues = [];

        $partSpecificationValues['d1']   = array_key_exists('d1', $item['features']) ? str_replace(",",".",$item['features']['d1'] ): null;
        $partSpecificationValues['d2']   = array_key_exists('d2', $item['features']) ? str_replace(",",".",$item['features']['d2'] ): null;
        $partSpecificationValues['d3']   = array_key_exists('d3', $item['features']) ? str_replace(",",".",$item['features']['d3'] ): null;
        $partSpecificationValues['l1']   = array_key_exists('l1', $item['features']) ? str_replace(",",".",$item['features']['l1'] ): null;
        $partSpecificationValues['l2']   = array_key_exists('l2', $item['features']) ? str_replace(",",".",$item['features']['l2'] ): null;
        $partSpecificationValues['l3']   = array_key_exists('l3', $item['features']) ? str_replace(",",".",$item['features']['l3'] ): null;
        $partSpecificationValues['A']    = array_key_exists('A', $item['features']) ? str_replace(",",".",$item['features']['A'] ): null;
        $partSpecificationValues['g']    = array_key_exists('g', $item['features']) ? str_replace(",",".",$item['features']['g'] ): null;


        $partSpecificationValuesFiltered = array_filter($partSpecificationValues, function ($item) {
            return $item != null;
        });

        return json_encode($partSpecificationValuesFiltered);
    }

    public function getImageAndDrawingLinks($group)
    {
        $acceptedExtensions = ['jpg', 'JPG', 'png', 'PNG'];

        $links = [];

        $links['image_links'] = [];

        $links['table_detail_image_link'] = '';

        foreach ($group->BILD as $picture) {
            if(in_array(pathinfo($picture)['extension'], $acceptedExtensions)){
                array_push($links['image_links'], env('APP_URL').'/storage/producers/diebold/partImages/'.urlencode($picture));
                if(Str::contains($picture, 'KM')){
                    $links['drawing_link'] = env('APP_URL').'/storage/producers/diebold/partImages/'.urlencode($picture);
                    $links['table_detail_image_link'] = env('APP_URL').'/storage/producers/diebold/partImages/'.urlencode($picture);
                }
                else {
                    $links['drawing_link'] = null;
                    if(array_key_exists('table_detail_image_link', $links)) {
                        $links['table_detail_image_link'] = env('APP_URL').'/storage/producers/diebold/partImages/'.urlencode($picture);
                    }
                }
            }
        }

        return $links;
    }

    public function getFeatures($part) {

        $featureKeys = [];

        $features = [];

        foreach ($part->MERKMAL as $feature) {
            array_push($featureKeys,(string)$feature->KUERZEL);
            $features[(string)$feature->KUERZEL] = (string)$feature->WERT;
        }

        return $features;
    }

}
