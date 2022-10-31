<?php

namespace App\Producers\Gloor\Jobs;

use App\Events\Import\ImportCompletedEvent;

use App\Producers\Gloor\ImportScripts\GloorBasicImport;
use App\Producers\Gloor\ImportScripts\GloorMapCategoriesAndGroups;
use App\Producers\Gloor\ImportScripts\GloorMapComparableData;
use App\Producers\Gloor\ImportScripts\GloorMapPdfData;
use App\Producers\Gloor\ImportScripts\GloorPriceImport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GloorPartImportJob

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
        $basicImport = new GloorBasicImport;
        $basicImport->basicImport($this->import);

        $mapCategoriesAndGroups = new GloorMapCategoriesAndGroups();
        $mapCategoriesAndGroups->mapCategories($this->import);

        $mapComparableData = new GloorMapComparableData();
        $mapComparableData->mapData($this->import);

        $priceImport = new GloorPriceImport();
        $priceImport->priceImport($this->import);

        $mapPdfData = new GloorMapPdfData;
        $mapPdfData->mapData($this->import);

        $this->import->update([
            'status'        =>  'imported',
            'notification'  =>  'Successfully imported '. Carbon::now()->format('d.m.Y H:i')
        ]);

        event(new ImportCompletedEvent($this->import));
    }
}
