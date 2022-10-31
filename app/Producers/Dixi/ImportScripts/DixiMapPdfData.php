<?php

namespace App\Producers\Dixi\ImportScripts;

use App\Producers\Dixi\Models\DixiPartsDe;
use App\Producers\Dixi\Models\DixiPartsEn;
use App\Producers\Dixi\Models\DixiPartsFr;
use App\Producers\Dixi\Models\DixiPartsIt;
use setasign\Fpdi\Fpdi;

class DixiMapPdfData
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

            $parts = DixiPartsDe::where('material_group',$rule->validation_string)->get();

            foreach ($parts as $part) {

                $part->update([
                    'inline_pdf_url' => $pdfUrl,
                    'inline_pdf_url_pages' => null,
                ]);;
            }
        }

    }

    public function importEnglishData()
    {
        $mapPdfRules = json_decode($this->import->pdf_mapping);

        foreach ($mapPdfRules as $rule) {

            $parts = DixiPartsEn::where('material_group',$rule->validation_string)->get();

            foreach ($parts as $part) {

                $part->update([
                    'inline_pdf_url' => $this->import->producer->unique_id.'/pdfs/'.$rule->file_name,
                    'inline_pdf_url_pages' => $rule->pages,
                ]);;
            }
        }
    }

    public function importFrenchData()
    {
        $mapPdfRules = json_decode($this->import->pdf_mapping);

        foreach ($mapPdfRules as $rule) {

            $parts = DixiPartsFr::where('material_group',$rule->validation_string)->get();

            foreach ($parts as $part) {

                $part->update([
                    'inline_pdf_url' => $this->import->producer->unique_id.'/pdfs/'.$rule->file_name,
                    'inline_pdf_url_pages' => $rule->pages,
                ]);;
            }
        }
    }

    public function importItalianData()
    {
        $mapPdfRules = json_decode($this->import->pdf_mapping);

        foreach ($mapPdfRules as $rule) {

            $parts = DixiPartsIt::where('material_group',$rule->validation_string)->get();

            foreach ($parts as $part) {

                $part->update([
                    'inline_pdf_url' => $this->import->producer->unique_id.'/pdfs/'.$rule->file_name,
                    'inline_pdf_url_pages' => $rule->pages,
                ]);;
            }
        }
    }


    public function getPdf($rule)
    {
        $pages = str_replace(',','_',$rule->pages);

        $fileName = explode('.',$rule->file_name)[0].'_'.$pages.'.pdf';

        if(!file_exists(public_path('storage/producers/dixi/pdfs/'.$fileName))) {
            $pdf = new FPDI();

            $pageCount = $pdf->setSourceFile(public_path('storage/producers/dixi/pdfs/' . $rule->file_name));

            $skipPages = explode(',', $rule->pages);


            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                if (!in_array($pageNo, $skipPages))
                    continue;

                $templateID = $pdf->importPage($pageNo);
                $pdf->getTemplateSize($templateID);
                $pdf->addPage();
                $pdf->useTemplate($templateID);
            }

            $pdf->Output(public_path('storage/producers/dixi/pdfs/' . $fileName), 'F');

        }
        return 'dixi/pdfs/'.$fileName;
    }
}
