<?php

namespace App\Producers\Diametal\Jobs;

use App\Events\Import\ImportCompletedEvent;
use App\Producers\Diebold\ImportScripts\DieboldAdditionalDataImport;
use App\Producers\Diebold\ImportScripts\DieboldBasicImport;
use App\Producers\Diebold\ImportScripts\DieboldMapCategoriesAndGroups;
use App\Producers\Diebold\ImportScripts\DieboldMapComparableData;
use App\Producers\Diebold\ImportScripts\DieboldPriceImport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DiametalImageImportJob

{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $files = Storage::disk('diametalImages')->allFiles();

        foreach ($files as $file) {

            $extension = explode('.', $file);

            $fileToCopy = Storage::disk('diametalImages')->get($file);

            if($extension[1] === 'pdf') {
                Storage::disk('localDiametalPdfs')->put($file, $fileToCopy);
            }
            else {
                Storage::disk('localDiametalPartImages')->put($file, $fileToCopy);
            }

            if(env('APP_ENV') === 'production') {
                Storage::disk('diametalImages')->delete($file);
            }

        }
    }
}
