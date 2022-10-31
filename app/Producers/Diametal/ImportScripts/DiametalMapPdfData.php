<?php

namespace App\Producers\Diametal\ImportScripts;

use App\Producers\Diametal\Models\DiametalPartsDe;
use App\Producers\Diametal\Models\DiametalPartsEn;
use setasign\Fpdi\Fpdi;

class DiametalMapPdfData
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
        }
    }

    public function importGermanData()
    {
        $mapPdfRules = json_decode($this->import->pdf_mapping);

        foreach ($mapPdfRules as $rule) {

            $pdfUrl = $this->getPdf($rule);

            $parts = DiametalPartsDe::where('part_name', 'like', '%'.$rule->validation_string.'%')->get();

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

            $parts = DiametalPartsEn::where('part_name', 'like', '%'.$rule->validation_string.'%')->get();

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

        if(!file_exists(public_path('storage/producers/diametal/pdfs/'.$fileName))) {
            $pdf = new FPDI();

            $pageCount = $pdf->setSourceFile(public_path('storage/producers/diametal/pdfs/' . $rule->file_name));

            $skipPages = explode(',', $rule->pages);


            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                if (!in_array($pageNo, $skipPages))
                    continue;

                $templateID = $pdf->importPage($pageNo);
                $pdf->getTemplateSize($templateID);
                $pdf->addPage();
                $pdf->useTemplate($templateID);
            }

            $pdf->Output(public_path('storage/producers/diametal/pdfs/' . $fileName), 'F');

        }
        return 'diametal/pdfs/'.$fileName;
    }
}
