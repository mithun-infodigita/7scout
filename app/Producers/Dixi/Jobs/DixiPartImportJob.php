<?php

namespace App\Producers\Dixi\Jobs;

use App\Events\Import\ImportCompletedEvent;

use App\Producers\Dixi\ImportScripts\DixiBasicImport;
use App\Producers\Dixi\ImportScripts\DixiMapCategoriesAndGroups;
use App\Producers\Dixi\ImportScripts\DixiMapComparableData;
use App\Producers\Dixi\ImportScripts\DixiMapPdfData;
use App\Producers\Dixi\ImportScripts\DixiPriceImport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DixiPartImportJob

{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public $import;

    public function __construct($import)
    {
        ini_set('memory_limit', '-1');

        $this->import = $import;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $basicImport = new DixiBasicImport();
        $basicImport->basicImport($this->import);

        $mapComparableData = new DixiMapComparableData();
        $mapComparableData->mapData($this->import);

        $mapCategoriesAndGroups = new DixiMapCategoriesAndGroups();
        $mapCategoriesAndGroups->mapCategories($this->import);

        $priceImportCH = new DixiPriceImport();
        $priceImportCH->priceImport($this->import);

        $mapPdfData = new DixiMapPdfData;
        $mapPdfData->mapData($this->import);

        $this->import->update([
            'status'        =>  'imported',
            'notification'  =>  'Successfully imported '. Carbon::now()->format('d.m.Y H:i')
        ]);

        event(new ImportCompletedEvent($this->import));
    }
}

