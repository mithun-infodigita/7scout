<?php

namespace App\Producers\Gloor\ImportScripts;

use App\Producers\Gloor\Models\GloorPartsDe;
use App\Producers\Gloor\Models\GloorPartsEn;
use App\Producers\Gloor\Models\GloorPartsFr;
use App\Producers\Gloor\Models\GloorPartsIt;
use setasign\Fpdi\Fpdi;

class GloorMapPdfData
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

            $parts = GloorPartsDe::where('part_number', '>=', $string[0])->where('part_number', '<=', $string[1])->get();

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

        if(!file_exists(public_path('storage/producers/gloor/pdfs/'.$fileName))) {
            $pdf = new FPDI();

            $pageCount = $pdf->setSourceFile(public_path('storage/producers/gloor/pdfs/' . $rule->file_name));

            $skipPages = explode(',', $rule->pages);;


            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                if (!in_array($pageNo, $skipPages))
                    continue;

                $templateID = $pdf->importPage($pageNo);
                $pdf->getTemplateSize($templateID);
                $pdf->addPage();
                $pdf->useTemplate($templateID);
            }

            $pdf->Output(public_path('storage/producers/gloor/pdfs/'.$fileName), 'F');
        }

        return 'gloor/pdfs/'.$fileName;
    }

    public function importEnglishData()
    {
        $mapPdfRules = json_decode($this->import->pdf_mapping);
        foreach ($mapPdfRules as $rule) {
            $string = explode(',', $rule->validation_string);

            $parts = GloorPartsEn::where('part_number', '>=', $string[0])->where('part_number', '<=', $string[1])->get();

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

            $parts = GloorPartsFr::where('part_number', '>=', $string[0])->where('part_number', '<=', $string[1])->get();

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

            $parts = GloorPartsIt::where('part_number', '>=', $string[0])->where('part_number', '<=', $string[1])->get();

            foreach ($parts as $part) {

                $part->update([
                    'inline_pdf_url' => $rule->url,
                    'inline_pdf_url_pages' => $rule->pages,
                ]);;
            }
        }
    }

}
