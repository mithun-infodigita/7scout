<?php

namespace App\Producers\Diebold\ImportScripts;


use App\Producers\Diebold\Models\DieboldPartsDe;
use App\Producers\Diebold\Models\DieboldPartsEn;
use Illuminate\Support\Collection;


class DieboldSpecialDataImport
{
    public $import;

    public function specialDataImport($import)
    {
        $this->import = $import;

        $this->deleteParts();

        $this->addParts();

        $this->addImage();

    }

    public function deleteParts()
    {
        $part = DieboldPartsDe::where('part_id', 'diebold_79323')->first();

        if($part) {
            $part->delete();
        }

        $part = DieboldPartsEn::where('part_id', 'diebold_79323')->first();

        if($part) {
            $part->delete();
        }
    }

    public function addParts()
    {
        DieboldPartsDe::updateOrCreate(
            [
                'part_id' => 'diebold_79336100'
            ],
            [
            "part_id"   =>  "diebold_79336100",
            "part_name" =>  "MS 3.0 Schrumpfgerät",
            "part_number"   =>  "79.336.100",
            "producer_id"   =>  "diebold",
            "producer_name" =>  "Diebold",
            "cat_level_1_id"    =>  5,
            "cat_level_1_name"  =>  "Schrumpftechnik",
            "cat_level_2_id"    =>  74,
            "cat_level_2_name"  =>  "Schrumpfgeräte",
            "group_id"          =>  71,
            "group_name"        =>  "Schrumpfgeräte",
            "discount_group"    =>  36,
            "country_of_origin" =>  "D",
            "customs_tariff_numbers"   => ["EU" => "85144000" ,"CH" => null  ],
            "price"             =>      ["currency"  => "EUR", "value" => "4280"],
            "preferential_beneficiary_eu"   =>  1,
            "stock"         =>  ["4" => 1],
            "stock_part_id"     =>  "79336100",
            "reprocurement_time"    =>  21,
            "weight"            =>  21.6,
            'part_specifications'           =>   json_encode([]),
            "part_description"      =>  json_encode(["Beschreibung" => "Das MS 3.0 wurde speziell für schlanke, dünnwandige und extrem kurze Schrumpffutter entwickelt
                    Es können auch Standardfutter bis Ø 16 mm eingeschrumpft werden
                    Die regelbare Generatorleistung erwärmt die Futter schonend
                    Auch bei Futtern mit kleinster Masse erstellt die Anlage vor dem Überhitzen rechtzeitig und prozesssicher ab
                    Geeignet für HM-Schäfte
                    Ø 3 -  16 mm bei Futtern mit Standardgeometrie oder
                    Ø 3 - 20 mm bei Futtern mit schlanker Geometrie",
                    "Schrumpflänge" =>	"ca. 250 mm",
                    "Lieferumfang" =>	"Inklusive 4 Ferrit-Wechselscheiben zum Schrumpfen von Ø 3 - Ø 16. und Schutzhandschuhe",
                    "Info"  =>  "Werkzeugaufnahme bitte separat bestellen."
            ]),
            "image_links" =>    "https://7scout.seventools.com/storage/producers/diebold/partImages/MS_3.jpg",
                "table_detail_image_link" =>    "https://7scout.seventools.com/storage/producers/diebold/partImages/MS_3.jpg"
        ]);

    }

    public function addImage()
    {
        $part = DieboldPartsDe::where('part_id', 'diebold_79500100')->first();

        $part->update([
            'image_links'       =>  'https://7scout.seventools.com/storage/producers/diebold/partImages/PB_79-500-100.jpg',
            'table_detail_image_link'       =>  'https://7scout.seventools.com/storage/producers/diebold/partImages/PB_79-500-100.jpg',
        ]);
    }
}
