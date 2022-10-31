<?php

namespace App\Producers\Motorex\ImportScripts;

use App\Producers\Motorex\Models\MotorexPartsDe;
use Illuminate\Support\Str;


class MotorexBasicImport
{
    public $import;

    public function basicImport($import)
    {
        MotorexPartsDe::updateOrCreate(
            [
                'part_id' => 'motorex_302312'
            ],
            $this->getAttributes1()
        );

        MotorexPartsDe::updateOrCreate(
            [
                'part_id' => 'motorex_303602'
            ],
            $this->getAttributes2()
        );

    }

    public function getAttributes1() {
        $attributes = [
            'producer_id'                   =>  'motorex',
            'producer_name'                 =>  'Motorex',
            'part_number'                   =>  '302312',
            'part_name'                     =>  'INTACT MX 50 SPRAY',
            'part_description'              =>  'Universell einsetzbarer Schmier- und Korrosionsschutzspray. Hinterlässt einen sehr wirksamen Schutzfilm. Verdrängt und unterwandert Wasser, hat hervorragende Kriecheigenschaften und verhindert Korrosion. Hinterlässt einen sehr wirksamen Schutzfilm.
                                                MOTOREX INTACT MX 50 ist unentbehrlich für Industrie, Gewerbe, Werkstatt und Garage. Schmiert alle beweglichen Teile, beseitigt Quietschen und ist auch bestens geeignet zum Schützen von Metalloberflächen und Konservieren von Werkzeugen.
                                                Aus ca. 5 – 10 cm Entfernung die Oberfläche kurz einsprühen und trocknen lassen. Bei Bedarf wiederholen.',
            'part_specifications'           =>   json_encode([
                                                   'Füllmenge'  =>  "500ml" ,
                                                    'Verpackungseinheit'    =>  "Karton à 12 Spraydosen 500ml",
                                                    'Eigenschaften'     =>  "- wasserverdrängend\n- schmierend\n - beseitigt Quietschen\n - kriechend\n - rostlösend\n - schützt vor Korrosion\n"
                                                ]),
            'stock_part_id'                 =>  '302312',
            'reprocurement_time'            =>  5,
            'weight'                        =>  "0.555",
            'customs_tariff_numbers'        =>  json_encode([ "EU" =>  'xxx', 'CH' =>   'yyy']),
            'preferential_beneficiary_eu'   =>  1,
            'country_of_origin'             =>  'DE',
            'stock'                         =>  json_encode([ "1" =>  0]),
            'price'                         =>  json_encode([
                "CH" =>[ "currency" =>  'CHF', 'value' =>   90],
                "LI" =>[ "currency" =>  'CHF', 'value' =>   90],
                "DE" =>[ "currency" =>  'EUR', 'value' =>   53.40]
            ]),
            'quantity'                      =>  "500ml",
            'table_detail_image_link'       =>  env('APP_URL').'/storage/producers/motorex/partImages/302312_intact_mx_50_spray_500ml_k12.jpg',
//            'material_group'                =>  1,
//            'discount_group'                =>  1,
         //   'image_links'                   =>      json_encode([$row['bild_1'], $row['bild_2']])
            'document_links'                =>  json_encode([
                ["link" => env('APP_URL').'/storage/producers/motorex/pdfs/PI_INTACT MX 50 SPRAY_DE.pdf', 'file_name' =>   'PI_INTACT MX 50 SPRAY_DE'],
                ["link" => env('APP_URL').'/storage/producers/motorex/pdfs/MSDS_INTACT_MX_50_SPRAY_DE_CH.pdf', 'file_name' =>   'MSDS_INTACT_MX_50_SPRAY_DE_CH']
            ]),
        ];


        return $attributes;
    }

    public function getAttributes2() {
        $attributes = [
            'producer_id'                   =>  'motorex',
            'producer_name'                 =>  'Motorex',
            'part_number'                   =>  '303602',
            'part_name'                     =>  'INTACT MX 50 SPRAY',
            'part_description'              =>  'Universell einsetzbarer Schmier- und Korrosionsschutzspray. Hinterlässt einen sehr wirksamen Schutzfilm. Verdrängt und unterwandert Wasser, hat hervorragende Kriecheigenschaften und verhindert Korrosion. Hinterlässt einen sehr wirksamen Schutzfilm.
                                                MOTOREX INTACT MX 50 ist unentbehrlich für Industrie, Gewerbe, Werkstatt und Garage. Schmiert alle beweglichen Teile, beseitigt Quietschen und ist auch bestens geeignet zum Schützen von Metalloberflächen und Konservieren von Werkzeugen.
                                                Aus ca. 5 – 10 cm Entfernung die Oberfläche kurz einsprühen und trocknen lassen. Bei Bedarf wiederholen.',
            'part_specifications'           =>   json_encode([
                'Füllmenge'  =>  "200ml" ,
                'Verpackungseinheit'    =>  "Karton à 12 Spraydosen 200ml",
                'Eigenschaften'     =>  "- wasserverdrängend\n- schmierend\n - beseitigt Quietschen\n - kriechend\n - rostlösend\n - schützt vor Korrosion\n"



            ]),
            'stock_part_id'                 =>  '303602',
            'reprocurement_time'            =>  5,
            'weight'                        =>  "0.555",
            'customs_tariff_numbers'        =>  json_encode([ "EU" =>  'xxx', 'CH' =>   'yyy']),
            'preferential_beneficiary_eu'   =>  1,
            'country_of_origin'             =>  'DE',
            'stock'                         =>  json_encode([ "1" =>  0]),
            'price'                         =>  json_encode([
                "CH" =>[ "currency" =>  'CHF', 'value' =>   64.80],
                "LI" =>[ "currency" =>  'CHF', 'value' =>   64.80],
                "DE" =>[ "currency" =>  'EUR', 'value' =>   43.20]
            ]),
            'quantity'                      =>  "200ml",
            'table_detail_image_link'       =>  env('APP_URL').'/storage/producers/motorex/partImages/302312_intact_mx_50_spray_500ml_k12.jpg',
//            'material_group'                =>  1,
//            'discount_group'                =>  1,
            //   'image_links'                   =>      json_encode([$row['bild_1'], $row['bild_2']])
            'document_links'                =>  json_encode([
                ["link" => env('APP_URL').'/storage/producers/motorex/pdfs/PI_INTACT MX 50 SPRAY_DE.pdf', 'file_name' =>   'PI_INTACT MX 50 SPRAY_DE'],
                ["link" => env('APP_URL').'/storage/producers/motorex/pdfs/MSDS_INTACT_MX_50_SPRAY_DE_CH.pdf', 'file_name' =>   'MSDS_INTACT_MX_50_SPRAY_DE_CH']
            ]),
        ];


        return $attributes;
    }
}

