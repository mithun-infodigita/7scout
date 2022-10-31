<?php

namespace App\Producers\Amf\ImportScripts;

use App\Producers\Amf\Models\AmfPartsDe;
use Illuminate\Support\Str;

class AmfBasicImport
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

        foreach ($xml->T_NEW_CATALOG->ARTICLE as $part) {
            $this->importPartsDe($part);
        }
    }

    public function importPartsDe($part)
    {
        $partId = 'amf_'.$part->SUPPLIER_AID;

        AmfPartsDe::updateOrCreate(
            ['part_id' => $partId],
            $this->getAttributes($part)
        );
    }

    public function getAttributes($part )
    {
        $imageLinks = $this->getImageLinks($part);

        $attributes = [
            'producer_id'                   =>  'amf',
            'producer_name'                 =>  'ANDREAS MAIER GmbH & Co. KG',
            'part_number'                   =>   $part->SUPPLIER_AID,
            'part_name'                     =>   $part->ARTICLE_DETAILS->DESCRIPTION_SHORT,
            'part_description'              =>   html_entity_decode(str_replace(['<br />', '<br>'], "\n", preg_replace("/\r|\n/", "", htmlspecialchars_decode($part->ARTICLE_DETAILS->DESCRIPTION_LONG[0]).htmlspecialchars_decode($part->ARTICLE_DETAILS->REMARKS[0])))),
            'part_specifications'           =>   $this->getPartSpecifications($part),
            'table_detail_image_link'       =>   array_key_exists(0, $imageLinks) ? $imageLinks[0] : null,
            'image_links'                   =>   json_encode($imageLinks),
            'reprocurement_time'            =>   21,
            'stock'                         =>   json_encode([ "7" =>  0]),
            'stock_part_id'                 =>   $part->SUPPLIER_AID
        ];

        return $attributes;
    }

    public function getPartSpecifications($part)
    {
        $partSpecifications = [];

        foreach ($part->ARTICLE_FEATURES as $features) {
            foreach ($features as $feature) {

                if(strlen($feature->FNAME) > 0) {
                    if(property_exists($feature,'FUNIT')) {

                        $partSpecifications[(string)$feature->FNAME] = (string)$feature->FVALUE[0].$feature->FUNIT;
                    }
                    else {
                        $partSpecifications[(string)$feature->FNAME] = (string)$feature->FVALUE[0];
                    }
                }
            }
        }

        return str_replace(['<br />', '<br>'],'', json_encode($partSpecifications));
    }


    public function getImageLinks($part)
    {
        $imageLinks = [];
        foreach ($part->MIME_INFO->MIME as $mime)
        {
            array_push($imageLinks, env('APP_URL').'/storage/producers/amf/partImages/'.explode('/', (string)$mime->MIME_SOURCE)[1]);
        }
        return $imageLinks;
    }
}
