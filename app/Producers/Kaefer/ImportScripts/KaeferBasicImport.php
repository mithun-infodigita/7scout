<?php


namespace App\Producers\Kaefer\ImportScripts;

use App\Models\CustomsSetting\CustomsSetting;

use App\Producers\Kaefer\Models\KaeferPartsDe;

use App\Producers\Kaefer\Models\KaeferPartsEn;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;


class KaeferBasicImport implements ToCollection, WithHeadingRow
{
    public $import;

    public $customsSettings;

    public $images;

    public function basicImport($import)
    {
        $this->customsSettings = CustomsSetting::all();

        $this->import = $import;

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

        $partId = $this->import->producer->unique_id . '_' . $row['artnr'];

        $attributes = $this->getAttributesDe($row);

        if($row['artnr']) {
            KaeferPartsDe::updateOrCreate(
                ['part_id' => $partId],
                $attributes
            );
        }
    }

    public function importPartsEn($row)
    {
        $row = $row->toArray();

        $partId = $this->import->producer->unique_id . '_' . $row['artnr'];

        $attributes = $this->getAttributesEn($row);

        if($row['artnr']) {
            KaeferPartsEn::updateOrCreate(
                ['part_id' => $partId],
                $attributes
            );
        }
    }


    public function getAttributesDe($row)
    {
        $attributes = [
            'producer_id'                   =>  'kaefer',
            'producer_name'                 =>  'Käfer Messuhrenfabrik',
            'scale'                         =>  array_key_exists('unterteilung_skalenteilungswert_de_eng', $row) ? $row['unterteilung_skalenteilungswert_de_eng'] : null,
            'part_number'                   =>  $row['artnr'],
            'part_name'                     =>  $row['bezeichnung_de'].' '.$row['artikelbezeichnung_d_eng'],
            'material_group'                =>  $row['preisgruppe'],
            'discount_group'                =>  $row['preisgruppe'],
            'material_tax_surcharge_group'  =>  $row['preisgruppe'],
            'country_of_origin'             =>  $row['praferenzieller_warenursprung'] === 'Deutschland' ? 'DE' : 'CH',
            'stock_part_id'                 =>  $row['artnr'],
            'reprocurement_time'            =>  5,
            'weight'                        =>  floatval($row['gewicht_in_g'] / 1000),
            'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
            'preferential_beneficiary_eu'   =>  1,
            'stock'                         =>  json_encode([ "8" =>  $row['verfugbare_losgrosse_stuck']]),
            'part_specifications'           =>  $this->getPartSpecificationsDe($row),
            'table_detail_image_link'       =>  $row['produktbild'] !== '/' ? env('APP_URL').'/storage/producers/kaefer/partImages/'.$this->getTableDetailImageLink($row) : null,
            'image_links'                   =>  $row['produktbild'] !== '/' ? env('APP_URL').'/storage/producers/kaefer/partImages/'.$this->getTableDetailImageLink($row) : null,
            'inline_pdf_url'                =>  array_key_exists('datenblatt', $row) ? 'kaefer/pdfs/'.$row['datenblatt'] : null,
            'sort_value'                    =>  array_key_exists('reihenfolge', $row) ? $row['reihenfolge'] : null,
            'mapping_helper'                =>  array_key_exists('unterteilung_de', $row) ? $row['unterteilung_de'] : null,
            'document_links'                =>  $this->getDocumentLinksDe($row)
        ];

        return $attributes;
    }


    public function getPartSpecificationsDe($row)
    {
        $partSpecifications = [
            'Skaleneinteilung'      =>  array_key_exists('skalenteilung', $row) ? $row['skalenteilung'] : null,
            'Messpanne'             =>  array_key_exists('messspanne', $row) ? $row['messspanne'] : null,
            '1 Zeigerumdrehung'     =>  array_key_exists('1_zeigerumdrehung', $row) ? $row['1_zeigerumdrehung'] : null,
            'Einspannschaft-Ø'      =>  array_key_exists('einspannschaft_o', $row) ? $row['einspannschaft_o'] : null,
            'Aussenring-Ø'          =>  array_key_exists('aussenring_o', $row) ? $row['aussenring_o'] : null,
            'Norm'                  =>  array_key_exists('norm_d_eng', $row) ? $row['norm_d_eng'] : null,
            'Schutzart'             =>  array_key_exists('schutzart', $row) ? $row['schutzart'] : null,
            'Anfangsmesskraft'      =>  array_key_exists('anfangsmesskraft', $row) ? $row['anfangsmesskraft'] : null,
            'Freihub'               =>  array_key_exists('freihub', $row) ? $row['freihub'] : null,
            'Skalenbezifferung'     =>  array_key_exists('skalenbezifferung', $row) ? $row['skalenbezifferung'] : null,
            'Messeinsatz'           =>  array_key_exists('messeinsatz', $row) ? $row['messeinsatz'] : null,
            'Länge Magnetfuss'      =>  array_key_exists('lange_magnetfuss', $row) ? $row['lange_magnetfuss']. ' mm' : null,
            'Höhe Magnetfuss'       =>  array_key_exists('hohe_magnetfuss', $row) ? $row['hohe_magnetfuss']. ' mm' : null,
            'Breite Magnetfuss'     =>  array_key_exists('breite_magnetfuss', $row) ? $row['breite_magnetfuss']. ' mm' : null,
            'Magnetische Haltkraft' =>  array_key_exists('magnetische_haltkraft', $row) ? $row['magnetische_haltkraft'] : null,
            'Länge der Quersäule'   =>  array_key_exists('lange_der_quersaule', $row) ? $row['lange_der_quersaule'] : null,
            'Durchmesser der Quersäule' =>  array_key_exists('durchmesser_der_quersaule', $row) ? $row['durchmesser_der_quersaule'] : null,
            'Länge der Vertikalsäule' =>  array_key_exists('lange_der_vertikalsaule', $row) ? $row['lange_der_vertikalsaule'] : null,
            'Durchmesser der Vertikalsäule' =>  array_key_exists('durchmesser_der_vertikalsaule', $row) ? $row['durchmesser_der_vertikalsaule'] : null,
            'Feineinstellung'      =>  array_key_exists('feineinstellung', $row) ? $row['feineinstellung'] : null,
            'Aufnahmebohrung für Messuhr'      =>  array_key_exists('aufnahmebohrung_fuer_messuhr', $row) ? $row['aufnahmebohrung_fuer_messuhr'] : null,
            'Bemerkung'             =>  array_key_exists('bemerkung', $row) ? $row['bemerkung'] : null,
        ];

        return json_encode(array_filter($partSpecifications));
    }

    public function getAttributesEn($row)
    {
        $attributes = [
            'producer_id'                   =>  'kaefer',
            'producer_name'                 =>  'Käfer Messuhrenfabrik',
            'scale'                         =>  array_key_exists('unterteilung_skalenteilungswert_de_eng', $row) ? $row['unterteilung_skalenteilungswert_de_eng'] : null,
            'part_number'                   =>  $row['artnr'],
            'part_name'                     =>  $row['bezeichnung_eng'].' '.$row['artikelbezeichnung_d_eng'],
            'material_group'                =>  $row['preisgruppe'],
            'discount_group'                =>  $row['preisgruppe'],
            'material_tax_surcharge_group'  =>  $row['preisgruppe'],
            'country_of_origin'             =>  $row['praferenzieller_warenursprung'] === 'Deutschland' ? 'DE' : 'CH',
            'stock_part_id'                 =>  $row['artnr'],
            'reprocurement_time'            =>  5,
            'weight'                        =>  floatval($row['gewicht_in_g'] / 1000),
            'customs_tariff_numbers'        =>  json_encode($this->getCustomsTariffNumbers($row)),
            'preferential_beneficiary_eu'   =>  1,
            'stock'                         =>  json_encode([ "8" =>  $row['verfugbare_losgrosse_stuck']]),
            'part_specifications'           =>  $this->getPartSpecificationsEn($row),
            'table_detail_image_link'       =>  $row['produktbild'] !== '/' ? env('APP_URL').'/storage/producers/kaefer/partImages/'.$this->getTableDetailImageLink($row) : null,
            'image_links'                   =>  $row['produktbild'] !== '/' ? env('APP_URL').'/storage/producers/kaefer/partImages/'.$this->getTableDetailImageLink($row) : null,
            'inline_pdf_url'                =>  array_key_exists('datenblatt', $row) ? 'kaefer/pdfs/'.$row['datenblatt'] : null,
            'sort_value'                    =>  array_key_exists('reihenfolge', $row) ? $row['reihenfolge'] : null,
            'mapping_helper'                =>  array_key_exists('unterteilung_de', $row) ? $row['unterteilung_de'] : null,
            'document_links'                =>  $this->getDocumentLinksEn($row)
        ];

        $attributes['part_name']            = str_replace('Sägeschränkmessuhr', 'Saw Setting Dial Gauge', $attributes['part_name'] );

        return $attributes;
    }


    public function getPartSpecificationsEn($row)
    {
        $partSpecifications = [
            'Reading'               =>  array_key_exists('skalenteilung', $row) ? $row['skalenteilung'] : null,
            'Range'                 =>  array_key_exists('messspanne', $row) ? $row['messspanne'] : null,
            'Range per revolution'  =>  array_key_exists('1_zeigerumdrehung', $row) ? $row['1_zeigerumdrehung'] : null,
            'Stem-Ø'                =>  array_key_exists('einspannschaft_o', $row) ? $row['einspannschaft_o'] : null,
            'Bezel-Ø'                       =>  array_key_exists('aussenring_o', $row) ? $row['aussenring_o'] : null,
            'Norm'                          =>  array_key_exists('norm_d_eng', $row) ? $row['norm_d_eng'] : null,
            'Protection class'              =>  array_key_exists('schutzart', $row) ? $row['schutzart'] : null,
            'Initial measuring force'       =>  array_key_exists('anfangsmesskraft', $row) ? $row['anfangsmesskraft'] : null,
            'Overtravel'                    =>  array_key_exists('freihub', $row) ? $row['freihub'] : null,
            'Dial Reading'                  =>  array_key_exists('skalenbezifferung', $row) ? $row['skalenbezifferung'] : null,
            'Contact point'           =>  array_key_exists('contact_point', $row) ? $row['contact_point'] : null,
        ];

        return json_encode(array_filter($partSpecifications));
    }

    public function getTableDetailImageLink($row)
    {
        return str_replace('tif', 'jpg', $row['produktbild']);
    }

    public function getCustomsTariffNumbers($row)
    {
        $tariffNumber = str_replace('.', '', $row['stat_waren_nr']);
        $customSetting = $this->customsSettings->where('customs_tariff_number_ch', $tariffNumber)->first();

        return [
            'EU'    =>  $tariffNumber,
            'CH'    =>  $customSetting ? $customSetting->customs_tariff_number_ch : null,
        ];
    }

    public function getDocumentLinksDe($row)
    {
        $documentLinks = [];

        array_key_exists('datenblatt', $row) && $row['datenblatt'] ? array_push($documentLinks, [
            'link' => env('APP_URL').'/storage/producers/kaefer/pdfs/'.$row['datenblatt'] ,
            'file_name' => $row['datenblatt']
        ])
        : null;
        array_key_exists('step_datei', $row) && $row['step_datei'] ? array_push($documentLinks, [
            'link' => env('APP_URL').'/storage/producers/kaefer/pdfs/'.$row['step_datei'] ,
            'file_name' => $row['step_datei']
        ])
            : null;

        return json_encode($documentLinks);
    }

    public function getDocumentLinksEn($row)
    {
        $documentLinks = [];

        array_key_exists('data_sheet', $row) && $row['data_sheet']? array_push($documentLinks, [
            'link' => env('APP_URL').'/storage/producers/kaefer/pdfs/'.$row['data_sheet'] ,
            'file_name' => $row['data_sheet']
        ])
            : null;
        array_key_exists('step_file', $row) && $row['step_file'] ? array_push($documentLinks, [
            'link' => env('APP_URL').'/storage/producers/kaefer/pdfs/'.$row['step_file'] ,
            'file_name' => $row['step_file']
        ])
            : null;

        return json_encode($documentLinks);
    }

}
