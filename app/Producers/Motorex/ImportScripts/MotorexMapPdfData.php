<?php

namespace App\Producers\Motorex\ImportScripts;

use App\Producers\Motorex\Models\MotorexPartsDe;
use App\Producers\Motorex\Models\MotorexPartsEn;
use App\Producers\Motorex\Models\MotorexPartsFr;
use App\Producers\Motorex\Models\MotorexPartsIt;
use setasign\Fpdi\Fpdi;

class MotorexMapPdfData
{
    public $import;

    public function mapData($import)
    {
        $this->import = $import;

        $this->mapPdfData();
    }

    public function mapPdfData()
    {
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
        $mapPdfRules = json_decode($this->import->pdf_mapping);

        foreach ($mapPdfRules as $rule) {

            $pdfUrl = $this->getPdf($rule);

            $string = explode(',', $rule->validation_string);

            $parts = MotorexPartsDe::all();

            foreach ($parts as $part) {

                $part->update([
                    'inline_pdf_url' => $pdfUrl,
                    'inline_pdf_url_pages' => null
                ]);;
            }
        }

    }

    public function getPdf($rule)
    {
        $pages = str_replace(',','_',$rule->pages);

        $fileName = explode('.',$rule->file_name)[0].'_'.$pages.'.pdf';

        if(!file_exists(public_path('storage/producers/motorex/pdfs/'.$fileName))) {
            $pdf = new FPDI();

            $pageCount = $pdf->setSourceFile(public_path('storage/producers/motorex/pdfs/' . $rule->file_name));

            $skipPages = explode(',', $rule->pages);;


            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                if (!in_array($pageNo, $skipPages))
                    continue;

                $templateID = $pdf->importPage($pageNo);
                $pdf->getTemplateSize($templateID);
                $pdf->addPage();
                $pdf->useTemplate($templateID);
            }

            $pdf->Output(public_path('storage/producers/motorex/pdfs/'.$fileName), 'F');
        }

        return 'motorex/pdfs/'.$fileName;
    }

    public function importEnglishData()
    {
        $mapPdfRules = json_decode($this->import->pdf_mapping);
        foreach ($mapPdfRules as $rule) {
            $string = explode(',', $rule->validation_string);

            $parts = MotorexPartsEn::where('part_number', '>=', $string[0])->where('part_number', '<=', $string[1])->get();

            foreach ($parts as $part) {

                $part->update([
                    'inline_pdf_url' => $rule->url,
                    'inline_pdf_url_pages' => $rule->pages,
                ]);;
            }
        }
    }

    public function importFrenchData()
    {
        $mapPdfRules = json_decode($this->import->pdf_mapping);
        foreach ($mapPdfRules as $rule) {
            $string = explode(',', $rule->validation_string);

            $parts = MotorexPartsFr::where('part_number', '>=', $string[0])->where('part_number', '<=', $string[1])->get();

            foreach ($parts as $part) {

                $part->update([
                    'inline_pdf_url' => $rule->url,
                    'inline_pdf_url_pages' => $rule->pages,
                ]);;
            }
        }
    }

    public function importItalianData()
    {
        $mapPdfRules = json_decode($this->import->pdf_mapping);

        foreach ($mapPdfRules as $rule) {
            $string = explode(',', $rule->validation_string);

            $parts = MotorexPartsIt::where('part_number', '>=', $string[0])->where('part_number', '<=', $string[1])->get();

            foreach ($parts as $part) {

                $part->update([
                    'inline_pdf_url' => $rule->url,
                    'inline_pdf_url_pages' => $rule->pages,
                ]);;
            }
        }
    }

}
