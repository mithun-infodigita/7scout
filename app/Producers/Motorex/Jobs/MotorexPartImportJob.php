<?php

namespace App\Producers\Motorex\Jobs;

use App\Events\Import\ImportCompletedEvent;

use App\Producers\Motorex\ImportScripts\MotorexAdditionalDataImport;
use App\Producers\Motorex\ImportScripts\MotorexBasicImport;
use App\Producers\Motorex\ImportScripts\MotorexMapCategoriesAndGroups;
use App\Producers\Motorex\ImportScripts\MotorexPriceImport;
use App\Producers\Motorex\ImportScripts\MotorexMapPdfData;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MotorexPartImportJob

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
        $basicImport = new MotorexBasicImport;
        $basicImport->basicImport($this->import);

        $basicImport = new MotorexMapCategoriesAndGroups;
        $basicImport->mapCategories($this->import);

        $mapPdfData = new MotorexMapPdfData;
        $mapPdfData->mapData($this->import);

        $this->import->update([
            'status'        =>  'imported',
            'notification'  =>  'Successfully imported '. Carbon::now()->format('d.m.Y H:i')
        ]);

        event(new ImportCompletedEvent($this->import));
    }
}
