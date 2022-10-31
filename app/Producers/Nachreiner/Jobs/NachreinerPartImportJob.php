<?php

namespace App\Producers\Nachreiner\Jobs;

use App\Events\Import\ImportCompletedEvent;

use App\Producers\Nachreiner\ImportScripts\NachreinerBasicImport;
use App\Producers\Nachreiner\ImportScripts\NachreinerMapCategoriesAndGroups;
use App\Producers\Nachreiner\ImportScripts\NachreinerMapComparableData;
use App\Producers\Nachreiner\ImportScripts\NachreinerMasterDataImport;
use App\Producers\Nachreiner\ImportScripts\NachreinerPriceImport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NachreinerPartImportJob implements ShouldQueue

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
        $basicImport = new NachreinerBasicImport;
        $basicImport->basicImport($this->import);

        $masterDataImport = new NachreinerMasterDataImport();
        $masterDataImport->masterDataImport($this->import);

        $mapCategoriesAndGroups = new NachreinerMapCategoriesAndGroups();
        $mapCategoriesAndGroups->mapCategories($this->import);

        $priceImport = new NachreinerPriceImport();
        $priceImport->priceImport($this->import);

        $mapComparableData = new NachreinerMapComparableData();
        $mapComparableData->mapData($this->import);

        $this->import->update([
            'status'        =>  'imported',
            'notification'  =>  'Successfully imported '. Carbon::now()->format('d.m.Y H:i')
        ]);

        event(new ImportCompletedEvent($this->import));
    }
}
