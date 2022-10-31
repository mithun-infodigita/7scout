<?php

namespace App\Producers\Amf\Jobs;

use App\Events\Import\ImportCompletedEvent;

use App\Producers\Amf\ImportScripts\AmfAdditionalDataImport;
use App\Producers\Amf\ImportScripts\AmfBasicImport;
use App\Producers\Amf\ImportScripts\AmfMapCategoriesAndGroups;
use App\Producers\Amf\ImportScripts\AmfPriceImport;
use App\Producers\Amf\ImportScripts\AmfUpdateMaterialGroupId;
use App\Producers\Diebold\ImportScripts\DieboldAdditionalDataImport;
use App\Producers\Diebold\ImportScripts\DieboldPriceImport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AmfPartImportJob

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
        $basicImport = new AmfBasicImport;
        $basicImport->basicImport($this->import);

        $additionalDataImport = new AmfAdditionalDataImport();
        $additionalDataImport->additionalDataImport($this->import);

        $updateMaterialGroupId = new AmfUpdateMaterialGroupId;
        $updateMaterialGroupId->updateMaterialGroupId($this->import);

        $updateMaterialGroupId = new AmfUpdateMaterialGroupId;
        $updateMaterialGroupId->updateMaterialGroupId($this->import);

        $mapCategoriesAndGroups = new AmfMapCategoriesAndGroups();
        $mapCategoriesAndGroups->mapCategories($this->import);

        $priceImport = new AmfPriceImport();
        $priceImport->priceImport($this->import);


        $this->import->update([
            'status'        =>  'imported',
            'notification'  =>  'Successfully imported '. Carbon::now()->format('d.m.Y H:i')
        ]);

        event(new ImportCompletedEvent($this->import));
    }
}
