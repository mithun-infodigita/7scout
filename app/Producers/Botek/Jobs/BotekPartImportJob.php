<?php

namespace App\Producers\Botek\Jobs;

use App\Events\Import\ImportCompletedEvent;
use App\Producers\Botek\ImportScripts\BotekBasicImport;
use App\Producers\Botek\ImportScripts\BotekMapCategoriesAndGroups;
use App\Producers\Botek\ImportScripts\BotekPriceImport;
use App\Producers\Botek\ImportScripts\BotekMapComparableData;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BotekPartImportJob

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
        $basicImport = new BotekBasicImport();
        $basicImport->basicImport($this->import);

        $mapCategoriesAndGroups = new BotekMapCategoriesAndGroups();
        $mapCategoriesAndGroups->mapCategories($this->import);

        $priceImport = new BotekPriceImport();
        $priceImport->priceImport($this->import);

        $mapComparableData = new BotekMapComparableData();
        $mapComparableData->mapData($this->import);

        $this->import->update([
            'status'        =>  'imported',
            'notification'  =>  'Successfully imported '. Carbon::now()->format('d.m.Y H:i')
        ]);

        event(new ImportCompletedEvent($this->import));
    }
}
