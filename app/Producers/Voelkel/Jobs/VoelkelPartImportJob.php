<?php

namespace App\Producers\Voelkel\Jobs;

use App\Events\Import\ImportCompletedEvent;;
use App\Producers\Voelkel\ImportScripts\VoelkelBasicImport;
use App\Producers\Voelkel\ImportScripts\VoelkelMapCategoriesAndGroups;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VoelkelPartImportJob

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

        $basicImport = new VoelkelBasicImport();
        $basicImport->basicImport($this->import);

        $mapCategoriesAndGroups = new VoelkelMapCategoriesAndGroups();
        $mapCategoriesAndGroups->mapCategories($this->import);

        $this->import->update([
            'status'        =>  'imported',
            'notification'  =>  'Successfully imported '. Carbon::now()->format('d.m.Y H:i')
        ]);

        event(new ImportCompletedEvent($this->import));
    }
}
