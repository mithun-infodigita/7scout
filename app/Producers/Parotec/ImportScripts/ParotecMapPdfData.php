<?php

namespace App\Producers\Parotec\ImportScripts;


use setasign\Fpdi\Fpdi;
use App\Producers\Parotec\Models\ParotecPartsDe;

class ParotecMapPdfData
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

        }
    }

    public function importGermanData()
    {
            $parts = ParotecPartsDe::all();

            foreach ($parts as $part) {

                $pdfUrl = $this->getPdf($part);

                $part->update([
                    'inline_pdf_url' => $pdfUrl
                ]);;
            }
    }

    public function getPdf($part)
    {
        $pages = str_replace(',','_',$part->inline_pdf_url_pages);

        $fileName = explode('.','A5_PowerGrip_Zusammenstellung')[0].'_'.$pages.'.pdf';

        if(!file_exists(public_path('storage/producers/parotec/pdfs/'.$fileName))) {
            $pdf = new FPDI();

            $pageCount = $pdf->setSourceFile(public_path('storage/producers/parotec/pdfs/A5_PowerGrip_Zusammenstellung.pdf'));

            $skipPages = explode(',', $part->inline_pdf_url_pages);


            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                if (!in_array($pageNo, $skipPages))
                    continue;

                $templateID = $pdf->importPage($pageNo);
                $pdf->getTemplateSize($templateID);
                $pdf->addPage();
                $pdf->useTemplate($templateID);
            }

            $pdf->Output(public_path('storage/producers/parotec/pdfs/' . $fileName), 'F');

        }
        return 'parotec/pdfs/'.$fileName;
    }
}
