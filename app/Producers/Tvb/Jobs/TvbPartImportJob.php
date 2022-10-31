<?php

namespace App\Producers\Tvb\Jobs;

use App\Events\Import\ImportCompletedEvent;
use App\Producers\Tvb\ImportScripts\TvbBasicImport;
use App\Producers\Tvb\ImportScripts\TvbMapCategoriesAndGroups;
use App\Producers\Tvb\ImportScripts\TvbPriceImport;
use App\Producers\Tvb\ImportScripts\TvbMapComparableData;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TvbPartImportJob

{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public $import;

    public function __construct($import)
    {
        $this->import = $import;

        ini_set('memory_limit', '-1');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $basicImport = new TvbBasicImport();
        $basicImport->basicImport($this->import);

        $mapCategoriesAndGroups = new TvbMapCategoriesAndGroups();
        $mapCategoriesAndGroups->mapCategories($this->import);

        $priceImport = new TvbPriceImport();
        $priceImport->priceImport($this->import);

        $mapComparableData = new TvbMapComparableData();
        $mapComparableData->mapData($this->import);

        $this->import->update([
            'status'        =>  'imported',
            'notification'  =>  'Successfully imported '. Carbon::now()->format('d.m.Y H:i')
        ]);

        event(new ImportCompletedEvent($this->import));
    }
}
