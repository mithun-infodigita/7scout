<?php

namespace App\Jobs\Import;


use App\Events\Import\ImportCompletedEvent;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NachreinerPartImportJob

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

        $this->import->update([
            'status'        =>  'imported',
            'notification'  =>  'Successfully imported '. Carbon::now()->format('d.m.Y H:i')
        ]);

        event(new ImportCompletedEvent($this->import));
    }
}
