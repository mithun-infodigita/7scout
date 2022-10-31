<?php

namespace App\Producers\Diametal\Jobs;

use App\Producers\Diametal\ImportScripts\DiametalBasicImport;
use App\Producers\Diametal\ImportScripts\DiametalMapCategoriesAndGroups;
use App\Producers\Diametal\ImportScripts\DiametalMapComparableData;
use App\Producers\Diametal\ImportScripts\DiametalMapPdfData;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DiametalPartImportJob

{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public $import;

    public function __construct($import)
    {
        $this->import = $import;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $basicImport = new DiametalBasicImport();
        $basicImport->basicImport();

        $mapCategoriesAndGroups = new DiametalMapCategoriesAndGroups();
        $mapCategoriesAndGroups->mapCategories($this->import);

        $mapComparableData = new DiametalMapComparableData();
        $mapComparableData->mapData($this->import);

        $mapPdfData = new DiametalMapPdfData;
        $mapPdfData->mapData($this->import);
    }
}
