<?php

namespace App\Producers\Parotec\Jobs;

use App\Events\Import\ImportCompletedEvent;
use App\Producers\Parotec\ImportScripts\ParotecMapPdfData;
use App\Producers\Parotec\ImportScripts\ParotecBasicImport;
use App\Producers\Parotec\ImportScripts\ParotecMapCategoriesAndGroups;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParotecPartImportJob

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
        $basicImport = new ParotecBasicImport;
        $basicImport->basicImport($this->import);

        $mapCategoriesAndGroups = new ParotecMapCategoriesAndGroups();
        $mapCategoriesAndGroups->mapCategories($this->import);

        $mapPdfData = new ParotecMapPdfData;
        $mapPdfData->mapData($this->import);

        $this->import->update([
            'status'        =>  'imported',
            'notification'  =>  'Successfully imported '. Carbon::now()->format('d.m.Y H:i')
        ]);

        event(new ImportCompletedEvent($this->import));
    }
}
