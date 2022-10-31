<?php

namespace App\Producers\Diametal\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DiametalMasterDataImportJob

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
        $files = Storage::disk('diametalMasterData')->allFiles();

        foreach ($files as $file) {

            $fileToCopy = Storage::disk('diametalMasterData')->get($file);

            $fileToCopyEncoded = mb_convert_encoding($fileToCopy, 'UTF-8', 'ISO-8859-1');

            Storage::disk('localDiametalMasterData')->put($file, $fileToCopyEncoded);

            if(env('APP_ENV') === 'production') {
                Storage::disk('diametalMasterData')->delete($file);
            }
        }
    }

}
