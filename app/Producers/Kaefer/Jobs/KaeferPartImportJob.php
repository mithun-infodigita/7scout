<?php

namespace App\Producers\Kaefer\Jobs;

use App\Events\Import\ImportCompletedEvent;
use App\Producers\Kaefer\ImportScripts\KaeferBasicImport;
use App\Producers\Kaefer\ImportScripts\StandardMapCategoriesAndGroups;
use App\Producers\Kaefer\ImportScripts\KaeferPriceImport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class KaeferPartImportJob

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
        $basicImport = new KaeferBasicImport();
        $basicImport->basicImport($this->import);

        $mapCategoriesAndGroups = new StandardMapCategoriesAndGroups();
        $mapCategoriesAndGroups->mapCategories($this->import);

        $priceImport = new KaeferPriceImport();
        $priceImport->priceImport($this->import);

        $this->import->update([
            'status'        =>  'imported',
            'notification'  =>  'Successfully imported '. Carbon::now()->format('d.m.Y H:i')
        ]);

        event(new ImportCompletedEvent($this->import));
    }
}
