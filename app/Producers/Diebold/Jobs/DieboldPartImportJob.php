<?php

namespace App\Producers\Diebold\Jobs;

use App\Events\Import\ImportCompletedEvent;
use App\Producers\Diebold\ImportScripts\DieboldAdditionalDataImport;
use App\Producers\Diebold\ImportScripts\DieboldBasicImport;
use App\Producers\Diebold\ImportScripts\DieboldMapCategoriesAndGroups;
use App\Producers\Diebold\ImportScripts\DieboldMapComparableData;
use App\Producers\Diebold\ImportScripts\DieboldPriceImport;
use App\Producers\Diebold\ImportScripts\DieboldSpecialDataImport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DieboldPartImportJob

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
//        $basicImport = new DieboldBasicImport;
//        $basicImport->basicImport($this->import);
//
//        $additionalDataImport = new DieboldAdditionalDataImport();
//        $additionalDataImport->additionalDataImport($this->import);
//
//        $mapCategoriesAndGroups = new DieboldMapCategoriesAndGroups();
//        $mapCategoriesAndGroups->mapCategories($this->import);

//        $mapComparableData = new DieboldMapComparableData();
//        $mapComparableData->mapData($this->import);

        $priceImport = new DieboldPriceImport();
        $priceImport->priceImport($this->import);
//
//        $specialDataImport = new DieboldSpecialDataImport;
//        $specialDataImport->specialDataImport($this->import);

        $this->import->update([
            'status'        =>  'imported',
            'notification'  =>  'Successfully imported '. Carbon::now()->format('d.m.Y H:i')
        ]);

        event(new ImportCompletedEvent($this->import));
    }
}
